# 🛠️ EXEMPLOS PRÁTICOS DE IMPLEMENTAÇÃO

## 1. 📈 **Implementação de DomPDF Real**

### Instalação
```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Atualização do PdfService
```php
<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfService
{
    public function generatePdf(string $view, array $data = [], array $options = [])
    {
        $pdf = Pdf::loadView($view, $data);
        
        // Configurações
        $pdf->setPaper($options['paper'] ?? 'A4', $options['orientation'] ?? 'portrait');
        
        if (isset($options['filename'])) {
            return $pdf->download($options['filename']);
        }
        
        return $pdf->stream();
    }
    
    public function generatePdfWithCharts(string $view, array $data = [])
    {
        // Gerar gráficos como imagens base64
        $chartImages = $this->generateChartImages($data);
        $data['chartImages'] = $chartImages;
        
        return $this->generatePdf($view, $data);
    }
    
    private function generateChartImages($data)
    {
        // Implementar geração de gráficos server-side
        // Usando bibliotecas como CpChart ou similar
        return [];
    }
}
```

## 2. 📊 **Sistema de Relatórios Agendados**

### Migration
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledReportsTable extends Migration
{
    public function up()
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // transactions, cash-flow, etc.
            $table->string('format'); // pdf, excel, csv
            $table->json('filters')->nullable();
            $table->string('schedule'); // cron expression
            $table->json('email_recipients');
            $table->boolean('active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_reports');
    }
}
```

### Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    protected $fillable = [
        'name', 'type', 'format', 'filters', 
        'schedule', 'email_recipients', 'active',
        'last_run_at', 'next_run_at', 'user_id'
    ];

    protected $casts = [
        'filters' => 'array',
        'email_recipients' => 'array',
        'active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateNextRun()
    {
        $cron = new \Cron\CronExpression($this->schedule);
        $this->next_run_at = $cron->getNextRunDate();
        $this->save();
    }
}
```

### Command para Executar Relatórios Agendados
```php
<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use App\Services\ReportExportService;
use App\Mail\ScheduledReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class GenerateScheduledReports extends Command
{
    protected $signature = 'reports:generate-scheduled';
    protected $description = 'Generate and send scheduled reports';

    public function handle(ReportExportService $exportService)
    {
        $reports = ScheduledReport::where('active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        foreach ($reports as $report) {
            try {
                $this->info("Generating report: {$report->name}");
                
                // Simular request com filtros
                $request = new \Illuminate\Http\Request($report->filters ?? []);
                
                // Gerar relatório
                $reportData = $exportService->generateReportData($report->type, $request);
                $pdf = app(PdfService::class)->generatePdf(
                    "reports.pdf.{$report->type}", 
                    $reportData
                );
                
                // Enviar por email
                foreach ($report->email_recipients as $email) {
                    Mail::to($email)->send(new ScheduledReportMail($report, $pdf));
                }
                
                // Atualizar próxima execução
                $report->last_run_at = now();
                $report->updateNextRun();
                
                $this->info("Report sent successfully to " . count($report->email_recipients) . " recipients");
                
            } catch (\Exception $e) {
                $this->error("Failed to generate report {$report->name}: " . $e->getMessage());
            }
        }
    }
}
```

## 3. 🎨 **Interface para Relatórios Agendados**

### Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\ScheduledReport;
use Illuminate\Http\Request;

class ScheduledReportController extends Controller
{
    public function index()
    {
        $reports = ScheduledReport::with('user')
            ->where('user_id', auth()->id())
            ->paginate(10);
            
        return view('reports.scheduled.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.scheduled.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:transactions,reconciliations,cash-flow,categories',
            'format' => 'required|in:pdf,excel,csv',
            'schedule' => 'required|string', // validar cron expression
            'email_recipients' => 'required|array',
            'email_recipients.*' => 'email',
            'filters' => 'nullable|array',
        ]);

        $report = ScheduledReport::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        $report->updateNextRun();

        return redirect()->route('reports.scheduled.index')
            ->with('success', 'Relatório agendado criado com sucesso!');
    }
}
```

### View para Criar Relatório Agendado
```blade
@extends('layouts.app')

@section('title', 'Agendar Relatório')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Agendar Novo Relatório</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.scheduled.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nome do Relatório</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Relatório</label>
                                <select name="type" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="transactions">Transações</option>
                                    <option value="reconciliations">Conciliações</option>
                                    <option value="cash-flow">Fluxo de Caixa</option>
                                    <option value="categories">Categorias</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Formato</label>
                                <select name="format" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Frequência</label>
                            <select id="scheduleType" class="form-select" onchange="updateSchedule()">
                                <option value="">Selecione...</option>
                                <option value="daily">Diário</option>
                                <option value="weekly">Semanal</option>
                                <option value="monthly">Mensal</option>
                                <option value="custom">Personalizado (Cron)</option>
                            </select>
                            <input type="hidden" name="schedule" id="scheduleInput">
                        </div>
                        
                        <div id="customSchedule" class="mb-3" style="display: none;">
                            <label class="form-label">Expressão Cron</label>
                            <input type="text" id="cronExpression" class="form-control" 
                                   placeholder="0 8 * * 1 (toda segunda às 8h)">
                            <small class="form-text text-muted">
                                Formato: minuto hora dia mês dia-da-semana
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Emails para Envio</label>
                            <div id="emailFields">
                                <div class="input-group mb-2">
                                    <input type="email" name="email_recipients[]" class="form-control" required>
                                    <button type="button" class="btn btn-outline-success" onclick="addEmailField()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.scheduled.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Agendar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSchedule() {
    const type = document.getElementById('scheduleType').value;
    const input = document.getElementById('scheduleInput');
    const customDiv = document.getElementById('customSchedule');
    
    switch(type) {
        case 'daily':
            input.value = '0 8 * * *'; // Todo dia às 8h
            customDiv.style.display = 'none';
            break;
        case 'weekly':
            input.value = '0 8 * * 1'; // Toda segunda às 8h
            customDiv.style.display = 'none';
            break;
        case 'monthly':
            input.value = '0 8 1 * *'; // Todo dia 1 às 8h
            customDiv.style.display = 'none';
            break;
        case 'custom':
            customDiv.style.display = 'block';
            break;
        default:
            input.value = '';
            customDiv.style.display = 'none';
    }
}

function addEmailField() {
    const container = document.getElementById('emailFields');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="email" name="email_recipients[]" class="form-control">
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

document.getElementById('cronExpression').addEventListener('input', function() {
    document.getElementById('scheduleInput').value = this.value;
});
</script>
@endsection
```

## 4. 📊 **Relatórios Comparativos**

### Service para Comparação
```php
<?php

namespace App\Services;

class ReportComparisonService
{
    public function compareTransactionReports($period1, $period2, $filters = [])
    {
        $data1 = $this->getTransactionData($period1, $filters);
        $data2 = $this->getTransactionData($period2, $filters);
        
        return [
            'period1' => $data1,
            'period2' => $data2,
            'comparison' => $this->calculateComparison($data1, $data2),
            'insights' => $this->generateInsights($data1, $data2)
        ];
    }
    
    private function calculateComparison($data1, $data2)
    {
        return [
            'total_change' => [
                'absolute' => $data2['total'] - $data1['total'],
                'percentage' => $data1['total'] > 0 ? 
                    (($data2['total'] - $data1['total']) / $data1['total']) * 100 : 0
            ],
            'credits_change' => [
                'absolute' => $data2['total_credit'] - $data1['total_credit'],
                'percentage' => $data1['total_credit'] > 0 ? 
                    (($data2['total_credit'] - $data1['total_credit']) / $data1['total_credit']) * 100 : 0
            ],
            'debits_change' => [
                'absolute' => $data2['total_debit'] - $data1['total_debit'],
                'percentage' => $data1['total_debit'] > 0 ? 
                    (($data2['total_debit'] - $data1['total_debit']) / $data1['total_debit']) * 100 : 0
            ]
        ];
    }
    
    private function generateInsights($data1, $data2)
    {
        $insights = [];
        
        // Análise de crescimento
        $growth = $data2['total'] - $data1['total'];
        if ($growth > 0) {
            $insights[] = "Crescimento positivo de " . number_format($growth, 2) . " no período";
        } elseif ($growth < 0) {
            $insights[] = "Redução de " . number_format(abs($growth), 2) . " no período";
        }
        
        // Análise de padrões
        if ($data2['transaction_count'] > $data1['transaction_count'] * 1.2) {
            $insights[] = "Aumento significativo no volume de transações";
        }
        
        return $insights;
    }
}
```

## 5. 🤖 **Sistema de Insights Automáticos**

### Service de Análise
```php
<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class InsightService
{
    public function generateTransactionInsights($data)
    {
        $insights = [];
        
        // Detectar anomalias
        $insights = array_merge($insights, $this->detectAnomalies($data));
        
        // Identificar tendências
        $insights = array_merge($insights, $this->identifyTrends($data));
        
        // Sugestões de otimização
        $insights = array_merge($insights, $this->generateOptimizations($data));
        
        return $insights;
    }
    
    private function detectAnomalies($data)
    {
        $anomalies = [];
        
        // Transações muito altas
        $avgAmount = $data->avg('amount');
        $highTransactions = $data->where('amount', '>', $avgAmount * 3);
        
        if ($highTransactions->count() > 0) {
            $anomalies[] = [
                'type' => 'high_value',
                'message' => "Encontradas {$highTransactions->count()} transações com valores muito altos",
                'severity' => 'warning',
                'details' => $highTransactions->take(3)->toArray()
            ];
        }
        
        // Padrões incomuns de horário
        $nightTransactions = $data->filter(function($transaction) {
            $hour = Carbon::parse($transaction->created_at)->hour;
            return $hour >= 22 || $hour <= 6;
        });
        
        if ($nightTransactions->count() > $data->count() * 0.1) {
            $anomalies[] = [
                'type' => 'unusual_timing',
                'message' => "Alto número de transações em horários incomuns",
                'severity' => 'info'
            ];
        }
        
        return $anomalies;
    }
    
    private function identifyTrends($data)
    {
        $trends = [];
        
        // Tendência de crescimento mensal
        $monthlyData = $data->groupBy(function($transaction) {
            return Carbon::parse($transaction->transaction_date)->format('Y-m');
        })->map(function($group) {
            return $group->sum('amount');
        });
        
        if ($monthlyData->count() >= 3) {
            $values = $monthlyData->values()->toArray();
            $trend = $this->calculateTrend($values);
            
            if ($trend > 0.1) {
                $trends[] = [
                    'type' => 'growth_trend',
                    'message' => "Tendência de crescimento consistente detectada",
                    'severity' => 'success',
                    'value' => round($trend * 100, 2) . '%'
                ];
            }
        }
        
        return $trends;
    }
    
    private function calculateTrend($values)
    {
        $n = count($values);
        if ($n < 2) return 0;
        
        $x = range(1, $n);
        $y = $values;
        
        $xy = array_sum(array_map(function($a, $b) { return $a * $b; }, $x, $y));
        $x_sum = array_sum($x);
        $y_sum = array_sum($y);
        $x2_sum = array_sum(array_map(function($a) { return $a * $a; }, $x));
        
        $slope = ($n * $xy - $x_sum * $y_sum) / ($n * $x2_sum - $x_sum * $x_sum);
        
        return $slope / array_sum($y) * $n; // Normalizado
    }
}
```

Essas implementações mostram como expandir significativamente o sistema atual, transformando-o em uma plataforma robusta de Business Intelligence. Cada funcionalidade pode ser implementada gradualmente, mantendo o sistema atual funcionando enquanto adiciona novas capacidades.
