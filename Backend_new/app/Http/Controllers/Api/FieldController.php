<?php

namespace App\Http\Controllers\Api;

use App\Models\FieldForm;
use App\Models\FieldSubmission;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FieldController extends \App\Http\Controllers\Controller
{
    /**
     * Get all field forms for a project
     */
    public function listForms(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $forms = $project->fieldForms()
            ->with(['submissions' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        return response()->json([
            'data' => $forms,
        ]);
    }

    /**
     * Create a new field form (JSON Schema)
     */
    public function storeForm(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schema_json' => 'required|json',
        ]);

        $form = $project->fieldForms()->create($validated);

        return response()->json([
            'message' => 'Field form created successfully',
            'data' => $form,
        ], 201);
    }

    /**
     * Get form details
     */
    public function showForm(Project $project, FieldForm $form): JsonResponse
    {
        $this->authorizeProject($project);
        if ($form->project_id !== $project->id) abort(404);

        return response()->json([
            'data' => $form,
        ]);
    }

    /**
     * Submit field data (can be offline)
     */
    public function submitData(Request $request, FieldForm $form): JsonResponse
    {
        $user = $request->user();

        // Authorize: user must be field agent or higher
        if (!in_array($user->role, ['field_agent', 'coordinator', 'se_manager', 'super_admin'])) {
            abort(403, 'Only field agents can submit data');
        }

        $validated = $request->validate([
            'data_json' => 'required|json',
            'gps_lat' => 'nullable|numeric|between:-90,90',
            'gps_lng' => 'nullable|numeric|between:-180,180',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'activity_id' => 'nullable|exists:activities,id',
        ]);

        $photosJson = null;
        if ($request->hasFile('photos')) {
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('field-submissions', 'public');
                $photoPaths[] = $path;
            }
            $photosJson = json_encode($photoPaths);
        }

        $submission = $form->submissions()->create([
            'agent_id' => $user->id,
            'activity_id' => $validated['activity_id'] ?? null,
            'data_json' => $validated['data_json'],
            'gps_lat' => $validated['gps_lat'] ?? null,
            'gps_lng' => $validated['gps_lng'] ?? null,
            'photos_json' => $photosJson,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Data submitted successfully',
            'submission_id' => $submission->id,
            'data' => $submission,
        ], 201);
    }

    /**
     * Get submissions for a form
     */
    public function getSubmissions(Request $request, Project $project, FieldForm $form): JsonResponse
    {
        $this->authorizeProject($project);
        if ($form->project_id !== $project->id) abort(404);

        $query = $form->submissions();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by agent
        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $submissions = $query
            ->with(['agent', 'activity'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $submissions,
        ]);
    }

    /**
     * Validate a field submission
     */
    public function validateSubmission(Request $request, Project $project, FieldSubmission $submission): JsonResponse
    {
        $this->authorizeProject($project);
        if ($submission->form->project_id !== $project->id) abort(404);

        // Authorization check
        if (!$request->user()->can('validate_data')) {
            abort(403, 'You cannot validate submissions');
        }

        $submission->validate();

        return response()->json([
            'message' => 'Submission validated successfully',
            'data' => $submission,
        ]);
    }

    /**
     * Reject a field submission
     */
    public function rejectSubmission(Request $request, Project $project, FieldSubmission $submission): JsonResponse
    {
        $this->authorizeProject($project);
        if ($submission->form->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $submission->update([
            'status' => 'rejected',
            'validation_notes' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Submission rejected',
            'data' => $submission,
        ]);
    }

    /**
     * Sync batch submissions (offline support)
     * Receives array of submissions to sync
     */
    public function syncBatch(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'submissions' => 'required|array',
            'submissions.*.form_id' => 'required|exists:field_forms,id',
            'submissions.*.data_json' => 'required|json',
            'submissions.*.gps_lat' => 'nullable|numeric',
            'submissions.*.gps_lng' => 'nullable|numeric',
        ]);

        $syncedCount = 0;
        $errors = [];

        foreach ($validated['submissions'] as $submissionData) {
            try {
                $form = FieldForm::find($submissionData['form_id']);
                
                $submission = $form->submissions()->create([
                    'agent_id' => $user->id,
                    'data_json' => $submissionData['data_json'],
                    'gps_lat' => $submissionData['gps_lat'] ?? null,
                    'gps_lng' => $submissionData['gps_lng'] ?? null,
                    'status' => 'synced',
                    'synced_at' => now(),
                ]);

                $syncedCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'form_id' => $submissionData['form_id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => "Synced $syncedCount submissions",
            'synced_count' => $syncedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Get submission location map data
     */
    public function getLocationData(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $submissions = $project->fieldForms()
            ->with('submissions')
            ->get()
            ->flatMap(fn($form) => $form->submissions)
            ->filter(fn($s) => $s->gps_lat && $s->gps_lng)
            ->map(fn($s) => [
                'id' => $s->id,
                'lat' => $s->gps_lat,
                'lng' => $s->gps_lng,
                'agent' => $s->agent->name,
                'activity' => $s->activity?->name ?? 'N/A',
                'created_at' => $s->created_at,
            ]);

        return response()->json([
            'data' => $submissions,
        ]);
    }

    /**
     * Authorize project access
     */
    private function authorizeProject(Project $project): void
    {
        if ($project->org_id !== auth()->user()->org_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access');
        }
    }
}
