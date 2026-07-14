<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $org = \App\Models\Organisation::factory()->create();
        $this->user = User::factory()->create(['role' => 'coordinator', 'org_id' => $org->id]);
        $this->project = Project::factory()->create(['org_id' => $org->id]);
    }

    public function test_it_can_generate_pdf_report()
    {
        Storage::fake('public');
        
        $response = $this->actingAs($this->user)->postJson("/api/v1/projects/{$this->project->id}/reports/generate", [
            'type' => 'full',
            'format' => 'pdf',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'file_path']]);

        $report = Report::find($response->json('data.id'));
        $this->assertNotNull($report);
        $this->assertEquals('generated', $report->status);
        $this->assertEquals('pdf', $report->parameters['format']);
        
        Storage::disk('public')->assertExists($report->file_path);
    }

    public function test_it_can_generate_excel_report()
    {
        Excel::fake();
        Storage::fake('public');
        
        $response = $this->actingAs($this->user)->postJson("/api/v1/projects/{$this->project->id}/reports/generate", [
            'type' => 'full',
            'format' => 'excel',
        ]);

        $response->assertStatus(201);
        $report = Report::find($response->json('data.id'));
        
        $this->assertEquals('generated', $report->status);
        $this->assertEquals('excel', $report->parameters['format']);

        Excel::assertStored($report->file_path, 'public');
    }

    public function test_it_can_download_report()
    {
        Storage::fake('public');
        $filePath = 'reports/test-report.pdf';
        Storage::disk('public')->put($filePath, 'fake pdf content');

        $report = Report::create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
            'title' => 'Test Report',
            'type' => 'full',
            'status' => 'generated',
            'file_path' => $filePath,
        ]);

        $response = $this->actingAs($this->user)->get("/api/v1/reports/{$report->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
