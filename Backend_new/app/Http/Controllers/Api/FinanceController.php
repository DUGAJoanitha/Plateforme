<?php

namespace App\Http\Controllers\Api;

use App\Models\BudgetLine;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends \App\Http\Controllers\Controller
{
    /**
     * Get all budget lines for a project
     */
    public function budgets(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $budgets = $project->budgetLines()
            ->with('expenses')
            ->get()
            ->map(fn($budget) => [
                'id' => $budget->id,
                'category' => $budget->category,
                'allocated' => $budget->allocated,
                'spent' => $budget->spent,
                'balance' => $budget->getBalance(),
                'consumption_percentage' => $budget->getConsumptionPercentage(),
                'alert_threshold' => $budget->alert_threshold,
                'should_alert' => $budget->shouldTriggerAlert(),
                'expenses_count' => $budget->expenses()->count(),
            ]);

        return response()->json([
            'data' => $budgets,
            'summary' => [
                'total_allocated' => $project->budgetLines()->sum('allocated'),
                'total_spent' => $project->budgetLines()->sum('spent'),
                'total_balance' => $project->budgetLines()->sum('allocated') - $project->budgetLines()->sum('spent'),
            ],
        ]);
    }

    /**
     * Create a budget line
     */
    public function storeBudget(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'allocated' => 'required|numeric|min:0',
            'alert_threshold' => 'numeric|between:0,100|default:90',
        ]);

        $budget = $project->budgetLines()->create($validated);

        return response()->json([
            'message' => 'Budget line created',
            'data' => $budget,
        ], 201);
    }

    /**
     * Submit an expense
     */
    public function submitExpense(Request $request, BudgetLine $budget): JsonResponse
    {
        $this->authorizeProject($budget->project);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,png,xlsx',
        ]);

        // Check budget availability
        if ($budget->spent + $validated['amount'] > $budget->allocated) {
            return response()->json([
                'error' => 'Insufficient budget. Available: ' . $budget->getBalance(),
            ], 422);
        }

        // Handle file upload
        $proofUrl = null;
        if ($request->hasFile('proof_file')) {
            $proofUrl = $request->file('proof_file')->store('expenses', 'public');
        }

        $expense = $budget->expenses()->create([
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'proof_url' => $proofUrl,
            'submitted_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Expense submitted for validation',
            'data' => $expense,
        ], 201);
    }

    /**
     * Validate an expense (by manager)
     */
    public function validateExpense(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeProject($expense->budgetLine->project);

        // Check if user has permission to validate
        if (!$request->user()->can('validate_expenses')) {
            abort(403, 'You do not have permission to validate expenses');
        }

        $validated = $request->validate([
            'comment' => 'nullable|string',
        ]);

        $expense->validate($request->user());

        // Update budget spent amount
        $budget = $expense->budgetLine;
        $budget->update(['spent' => $budget->spent + $expense->amount]);

        // Check alert threshold
        if ($budget->shouldTriggerAlert()) {
            // TODO: Send alert notification
        }

        return response()->json([
            'message' => 'Expense validated',
            'data' => $expense,
        ]);
    }

    /**
     * Reject an expense
     */
    public function rejectExpense(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeProject($expense->budgetLine->project);

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $expense->reject($request->user());

        return response()->json([
            'message' => 'Expense rejected',
            'data' => $expense,
        ]);
    }

    /**
     * Get financial summary
     */
    public function summary(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $budgets = $project->budgetLines()->get();
        $totalAllocated = $budgets->sum('allocated');
        $totalSpent = $budgets->sum('spent');

        $budgetsByCategory = $budgets->groupBy('category')->map(fn($group) => [
            'category' => $group->first()->category,
            'allocated' => $group->sum('allocated'),
            'spent' => $group->sum('spent'),
            'percentage' => $group->sum('allocated') > 0 ? ($group->sum('spent') / $group->sum('allocated')) * 100 : 0,
        ]);

        return response()->json([
            'summary' => [
                'total_budget' => $totalAllocated,
                'total_spent' => $totalSpent,
                'remaining' => $totalAllocated - $totalSpent,
                'consumption_percentage' => $totalAllocated > 0 ? ($totalSpent / $totalAllocated) * 100 : 0,
            ],
            'by_category' => $budgetsByCategory,
            'alert_count' => $budgets->filter(fn($b) => $b->shouldTriggerAlert())->count(),
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
