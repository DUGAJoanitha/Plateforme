<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectStatusExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $projects;

    public function __construct($projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        return $this->projects;
    }

    public function map($project): array
    {
        $allocated = $project->budgetLines->sum('allocated') ?? 0;
        $spent = $project->budgetLines->sum('spent') ?? 0;
        $burnRate = $allocated > 0 ? round(($spent / $allocated) * 100, 2) . '%' : '0%';

        $completed = $project->activities->where('status', 'completed')->count();
        $total = $project->activities->count();
        $progress = $total > 0 ? round(($completed / $total) * 100, 2) . '%' : '0%';

        return [
            $project->id,
            $project->name,
            $project->status,
            $project->start_date ? $project->start_date->format('Y-m-d') : 'N/A',
            $project->end_date ? $project->end_date->format('Y-m-d') : 'N/A',
            number_format($allocated, 2, '.', ''),
            number_format($spent, 2, '.', ''),
            $burnRate,
            $progress,
            $project->risks->where('score', '>=', 75)->count(),
        ];
    }

    public function headings(): array
    {
        return [
            'ID Projet',
            'Nom du Projet',
            'Statut',
            'Date Début',
            'Date Fin',
            'Budget Alloué',
            'Budget Consommé',
            'Taux Consommation',
            'Progression',
            'Risques Critiques',
        ];
    }

    public function title(): string
    {
        return 'Statut des Projets';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']]],
        ];
    }
}
