<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport - {{ $report->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }
        
        /* Header */
        .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 30px; text-align: center; }
        .header .logo-title { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .header .subtitle { font-size: 13px; opacity: 0.85; margin-top: 5px; }
        .header .report-title { font-size: 18px; font-weight: bold; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3); }

        /* Meta */
        .meta-bar { background: #f1f5f9; padding: 12px 30px; display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; font-size: 11px; color: #64748b; }

        /* Content */
        .content { padding: 25px 30px; }
        h2 { font-size: 15px; font-weight: bold; color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 6px; margin: 20px 0 12px 0; }
        h3 { font-size: 13px; font-weight: bold; color: #374151; margin: 15px 0 8px 0; }

        /* KPI Cards */
        .kpi-grid { display: flex; gap: 12px; margin-bottom: 20px; }
        .kpi-card { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; text-align: center; }
        .kpi-card .value { font-size: 22px; font-weight: bold; color: #1e40af; }
        .kpi-card .label { font-size: 10px; color: #64748b; margin-top: 4px; text-transform: uppercase; }
        .kpi-card.green .value { color: #16a34a; }
        .kpi-card.red .value { color: #dc2626; }
        .kpi-card.orange .value { color: #d97706; }

        /* Progress bar */
        .progress-wrap { background: #e5e7eb; border-radius: 99px; height: 12px; margin-bottom: 5px; }
        .progress-fill { height: 12px; border-radius: 99px; background: #3b82f6; }
        .progress-fill.success { background: #16a34a; }
        .progress-fill.warning { background: #d97706; }
        .progress-fill.danger { background: #dc2626; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #1e40af; color: white; padding: 9px 12px; text-align: left; font-size: 11px; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) td { background: #f8fafc; }

        /* Status badges */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f1f5f9; color: #475569; }

        /* AI Section */
        .ai-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .ai-box .ai-header { display: flex; align-items: center; gap: 8px; font-weight: bold; color: #0369a1; margin-bottom: 10px; }
        .rec-item { padding: 8px 12px; background: white; border-left: 3px solid #3b82f6; margin-bottom: 8px; border-radius: 0 6px 6px 0; }
        .rec-item.critical { border-color: #dc2626; }
        .rec-item.high { border-color: #d97706; }
        .rec-item.medium { border-color: #3b82f6; }
        .rec-item .rec-title { font-weight: bold; font-size: 12px; }
        .rec-item .rec-desc { font-size: 11px; color: #4b5563; margin-top: 3px; }

        /* Footer */
        .footer { border-top: 2px solid #e2e8f0; padding: 15px 30px; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; margin-top: 30px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo-title">🎯 PISE-PP</div>
        <div class="subtitle">Plateforme Intelligente de Suivi-Évaluation de Projets et Programmes</div>
        <div class="report-title">{{ $report->title }}</div>
    </div>

    {{-- META BAR --}}
    <div class="meta-bar">
        <span>📅 Généré le : {{ now()->format('d/m/Y à H:i') }}</span>
        <span>📋 Projet : {{ $project->name }}</span>
        <span>🏢 Organisation : {{ $project->organisation->name ?? 'N/A' }}</span>
        <span>👤 Par : {{ $report->user->name ?? 'Système' }}</span>
    </div>

    <div class="content">

        {{-- ═══ RÉSUMÉ DU PROJET ═══ --}}
        <h2>📊 Résumé Exécutif du Projet</h2>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="value">{{ $stats['progress_pct'] }}%</div>
                <div class="label">Avancement Global</div>
            </div>
            <div class="kpi-card {{ $stats['budget_pct'] > 90 ? 'red' : ($stats['budget_pct'] > 75 ? 'orange' : 'green') }}">
                <div class="value">{{ $stats['budget_pct'] }}%</div>
                <div class="label">Budget Consommé</div>
            </div>
            <div class="kpi-card {{ $stats['kpi_on_track'] < $stats['kpi_total'] ? 'orange' : 'green' }}">
                <div class="value">{{ $stats['kpi_on_track'] }}/{{ $stats['kpi_total'] }}</div>
                <div class="label">KPIs dans les Cibles</div>
            </div>
            <div class="kpi-card {{ $stats['delayed_activities'] > 0 ? 'red' : 'green' }}">
                <div class="value">{{ $stats['delayed_activities'] }}</div>
                <div class="label">Activités en Retard</div>
            </div>
        </div>

        {{-- Barre de progression --}}
        @php
            $pct = $stats['progress_pct'];
            $cls = $pct >= 70 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
        @endphp
        <p style="font-size:11px;color:#64748b;margin-bottom:4px;">Progression des activités</p>
        <div class="progress-wrap"><div class="progress-fill {{ $cls }}" style="width:{{ $pct }}%"></div></div>
        <p style="font-size:11px;color:#64748b;text-align:right;margin-bottom:15px;">{{ $pct }}% complété</p>

        {{-- ═══ ACTIVITÉS ═══ --}}
        @if(isset($activities) && $activities->count())
        <h2>📌 Activités du Projet</h2>
        <table>
            <thead>
                <tr>
                    <th>Activité</th>
                    <th>Début</th>
                    <th>Fin prévue</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @foreach($activities as $activity)
                @php
                    $badgeClass = match($activity->status) {
                        'completed' => 'badge-green',
                        'in_progress' => 'badge-blue',
                        'blocked' => 'badge-red',
                        default => 'badge-gray'
                    };
                    $statusLabel = match($activity->status) {
                        'completed' => '✅ Terminée',
                        'in_progress' => '🔄 En cours',
                        'blocked' => '🔴 Bloquée',
                        default => '⏳ ' . ucfirst($activity->status)
                    };
                @endphp
                <tr>
                    <td>{{ $activity->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($activity->end_date)->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        {{-- ═══ KPIs ═══ --}}
        @if(isset($kpis) && $kpis->count())
        <h2>🎯 Indicateurs Clés de Performance (KPI)</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th>Cible</th>
                    <th>Valeur Actuelle</th>
                    <th>Unité</th>
                    <th>Réalisation</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @foreach($kpis as $kpi)
                @php
                    $pctKpi = $kpi->target_value > 0 ? min(100, round(($kpi->current_value / $kpi->target_value) * 100)) : 0;
                    $onTrack = $kpi->isOnTrack();
                @endphp
                <tr>
                    <td>{{ $kpi->name }}</td>
                    <td>{{ number_format($kpi->target_value, 2) }}</td>
                    <td>{{ number_format($kpi->current_value, 2) }}</td>
                    <td>{{ $kpi->unit }}</td>
                    <td>
                        <div class="progress-wrap" style="height:8px;margin-bottom:2px">
                            <div class="progress-fill {{ $onTrack ? 'success' : 'danger' }}" style="width:{{ $pctKpi }}%"></div>
                        </div>
                        {{ $pctKpi }}%
                    </td>
                    <td><span class="badge {{ $onTrack ? 'badge-green' : 'badge-red' }}">{{ $onTrack ? '✅ En cible' : '⚠️ Hors cible' }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        {{-- ═══ FINANCES ═══ --}}
        @if(isset($budgetLines) && $budgetLines->count())
        <h2>💰 Synthèse Financière</h2>
        <table>
            <thead>
                <tr>
                    <th>Ligne Budgétaire</th>
                    <th>Alloué (FCFA)</th>
                    <th>Dépensé (FCFA)</th>
                    <th>Solde (FCFA)</th>
                    <th>Utilisation</th>
                </tr>
            </thead>
            <tbody>
            @php $totAlloc = 0; $totSpent = 0; @endphp
            @foreach($budgetLines as $line)
                @php
                    $bal = $line->allocated - $line->spent;
                    $pctLine = $line->allocated > 0 ? min(100, round(($line->spent / $line->allocated) * 100)) : 0;
                    $totAlloc += $line->allocated;
                    $totSpent += $line->spent;
                @endphp
                <tr>
                    <td>{{ $line->category }}</td>
                    <td>{{ number_format($line->allocated, 0, ',', ' ') }}</td>
                    <td>{{ number_format($line->spent, 0, ',', ' ') }}</td>
                    <td style="color:{{ $bal < 0 ? '#dc2626' : '#16a34a' }}">{{ number_format($bal, 0, ',', ' ') }}</td>
                    <td>
                        <div class="progress-wrap" style="height:8px;margin-bottom:2px">
                            <div class="progress-fill {{ $pctLine > 90 ? 'danger' : ($pctLine > 75 ? 'warning' : 'success') }}" style="width:{{ $pctLine }}%"></div>
                        </div>
                        {{ $pctLine }}%
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <p style="text-align:right;font-weight:bold;font-size:12px;margin-top:-10px;margin-bottom:20px;">
            Total dépensé : <span style="color:#1e40af">{{ number_format($totSpent, 0, ',', ' ') }} / {{ number_format($totAlloc, 0, ',', ' ') }} FCFA</span>
        </p>
        @endif

        {{-- ═══ RECOMMANDATIONS IA ═══ --}}
        @if(isset($aiRecommendations) && count($aiRecommendations))
        <h2>🤖 Recommandations Intelligentes (IA)</h2>
        <div class="ai-box">
            <div class="ai-header">🧠 Analyse générée par le moteur d'intelligence artificielle PISE-PP</div>
            @foreach($aiRecommendations as $rec)
                @php
                    $content = is_array($rec->content) ? $rec->content : json_decode($rec->content, true);
                    $priority = strtolower($content['priority'] ?? 'medium');
                @endphp
                <div class="rec-item {{ $priority }}">
                    <div class="rec-title">{{ $content['title'] ?? 'Recommandation' }}</div>
                    <div class="rec-desc">{{ $content['description'] ?? $rec->content }}</div>
                </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <span>📄 PISE-PP — Rapport confidentiel</span>
        <span>Projet : {{ $project->name }}</span>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>

</body>
</html>
