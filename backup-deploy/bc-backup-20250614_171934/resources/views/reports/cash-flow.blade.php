@extends('layouts.app')

@section('title', 'Relatório de Fluxo de Caixa')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-chart-line text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Relatório de Fluxo de Caixa</h2>
        <p class="text-muted mb-0">Análise de entradas e saídas financeiras por período</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-success" onclick="exportData('excel')">
        <i class="fas fa-file-excel me-2"></i>Excel
    </button>
    <button class="btn btn-outline-danger" onclick="exportData('pdf')">
        <i class="fas fa-file-pdf me-2"></i>PDF
    </button>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimir
    </button>
</div>
@endsection

@section('content')
<!-- Filtros e Controles -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros e Período
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.cash-flow') }}" id="cashFlowForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-success me-2"></i>Data Inicial
                            </label>
                            <input type="date" name="start_date" class="form-control form-control-lg" 
                                   value="{{ $startDate }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-check text-info me-2"></i>Data Final
                            </label>
                            <input type="date" name="end_date" class="form-control form-control-lg" 
                                   value="{{ $endDate }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-university text-warning me-2"></i>Conta Bancária
                            </label>
                            <select name="bank_account_id" class="form-select form-select-lg">
                                <option value="">Todas as contas</option>
                                @if(isset($bankAccounts))
                                    @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} - {{ $account->bank_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('today')">
                                        Hoje
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('week')">
                                        Esta Semana
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('month')">
                                        Este Mês
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('year')">
                                        Este Ano
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-chart-line me-2"></i>Gerar Relatório
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Resumo -->
@if($dailyFlow->count() > 0)
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-arrow-up fa-2x text-success"></i>
                </div>
                <h6 class="text-muted">Total de Entradas</h6>
                <h3 class="text-success mb-0">
                    R$ {{ number_format($dailyFlow->sum('credits'), 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-arrow-down fa-2x text-danger"></i>
                </div>
                <h6 class="text-muted">Total de Saídas</h6>
                <h3 class="text-danger mb-0">
                    R$ {{ number_format($dailyFlow->sum('debits'), 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-balance-scale fa-2x text-info"></i>
                </div>
                <h6 class="text-muted">Saldo Líquido</h6>
                @php
                    $netBalance = $dailyFlow->sum('credits') - $dailyFlow->sum('debits');
                @endphp
                <h3 class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                    R$ {{ number_format($netBalance, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-chart-pie fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted">Saldo Final</h6>
                <h3 class="{{ $dailyFlow->last()->cumulative_balance >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                    R$ {{ number_format($dailyFlow->last()->cumulative_balance, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tabela de Fluxo de Caixa -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table text-primary me-2"></i>Fluxo de Caixa Detalhado
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#tableFilters">
                    <i class="fas fa-filter me-2"></i>Filtros
                </button>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleView('table')" id="tableViewBtn">
                        <i class="fas fa-table me-2"></i>Tabela
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleView('chart')" id="chartViewBtn">
                        <i class="fas fa-chart-line me-2"></i>Gráfico
                    </button>
                </div>
            </div>
        </div>
        
        <div class="collapse mt-3" id="tableFilters">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="searchDates" placeholder="Buscar por data...">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="filterBalance">
                        <option value="">Todos os saldos</option>
                        <option value="positive">Saldos Positivos</option>
                        <option value="negative">Saldos Negativos</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control" id="minAmount" placeholder="Valor mínimo">
                        <span class="input-group-text">até</span>
                        <input type="number" class="form-control" id="maxAmount" placeholder="Valor máximo">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-secondary w-100" onclick="clearTableFilters()">
                        Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Visualização em Tabela -->
        <div id="tableView">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="cashFlowTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-4 py-3">
                                <i class="fas fa-calendar me-2 text-muted"></i>Data
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-arrow-up me-2 text-success"></i>Entradas
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-arrow-down me-2 text-danger"></i>Saídas
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-balance-scale me-2 text-info"></i>Saldo do Dia
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-chart-line me-2 text-primary"></i>Saldo Acumulado
                            </th>
                            <th class="border-0 px-4 py-3 text-center">
                                <i class="fas fa-chart-pie me-2 text-muted"></i>Variação
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyFlow as $index => $day)
                        <tr class="cash-flow-row">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="fas fa-calendar-day text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $day->transaction_date->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $day->transaction_date->format('l') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold text-success">
                                    <i class="fas fa-plus-circle me-1"></i>
                                    R$ {{ number_format($day->credits, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold text-danger">
                                    <i class="fas fa-minus-circle me-1"></i>
                                    R$ {{ number_format($day->debits, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold {{ $day->daily_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-{{ $day->daily_balance >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                    R$ {{ number_format($day->daily_balance, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="badge bg-{{ $day->cumulative_balance >= 0 ? 'success' : 'danger' }} fs-6">
                                    R$ {{ number_format($day->cumulative_balance, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($index > 0)
                                    @php
                                        $previousBalance = $dailyFlow[$index - 1]->cumulative_balance;
                                        $variation = (($day->cumulative_balance - $previousBalance) / abs($previousBalance)) * 100;
                                    @endphp
                                    <span class="badge bg-{{ $variation >= 0 ? 'success' : 'danger' }} bg-opacity-25 text-{{ $variation >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $variation >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                        {{ number_format(abs($variation), 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center">
                                <div class="py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma movimentação encontrada</h5>
                                    <p class="text-muted mb-0">Não há transações para o período selecionado.</p>
                                    <button class="btn btn-outline-primary mt-3" onclick="adjustPeriod()">
                                        <i class="fas fa-calendar-alt me-2"></i>Ajustar Período
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($dailyFlow->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th class="px-4 py-3">
                                <strong>TOTAIS DO PERÍODO</strong>
                            </th>
                            <th class="px-4 py-3 text-end text-success">
                                <strong>R$ {{ number_format($dailyFlow->sum('credits'), 2, ',', '.') }}</strong>
                            </th>
                            <th class="px-4 py-3 text-end text-danger">
                                <strong>R$ {{ number_format($dailyFlow->sum('debits'), 2, ',', '.') }}</strong>
                            </th>
                            <th class="px-4 py-3 text-end">
                                @php $totalNet = $dailyFlow->sum('credits') - $dailyFlow->sum('debits'); @endphp
                                <strong class="{{ $totalNet >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($totalNet, 2, ',', '.') }}
                                </strong>
                            </th>
                            <th class="px-4 py-3 text-end">
                                <strong class="{{ $dailyFlow->last()->cumulative_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($dailyFlow->last()->cumulative_balance, 2, ',', '.') }}
                                </strong>
                            </th>
                            <th class="px-4 py-3 text-center">
                                <span class="text-muted">-</span>
                            </th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Visualização em Gráfico -->
        <div id="chartView" style="display: none;">
            <div class="p-4">
                <canvas id="cashFlowChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.cash-flow-row:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.875em;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem 0.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .px-4 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Inicializar gráfico se há dados
    @if($dailyFlow->count() > 0)
        initializeChart();
    @endif
    
    // Filtros de tabela
    $('#searchDates, #filterBalance, #minAmount, #maxAmount').on('change keyup', function() {
        filterTable();
    });
    
    // Atalhos de teclado
    $(document).on('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.which) {
                case 80: // Ctrl+P - Imprimir
                    e.preventDefault();
                    window.print();
                    break;
                case 69: // Ctrl+E - Exportar Excel
                    e.preventDefault();
                    exportData('excel');
                    break;
                case 84: // Ctrl+T - Alternar visão
                    e.preventDefault();
                    const currentView = $('#tableView').is(':visible') ? 'chart' : 'table';
                    toggleView(currentView);
                    break;
            }
        }
    });
});

function setQuickPeriod(period) {
    const today = new Date();
    let startDate, endDate = today;
    
    switch(period) {
        case 'today':
            startDate = today;
            break;
        case 'week':
            startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            break;
    }
    
    $('input[name="start_date"]').val(startDate.toISOString().split('T')[0]);
    $('input[name="end_date"]').val(endDate.toISOString().split('T')[0]);
}

function toggleView(view) {
    if (view === 'chart') {
        $('#tableView').hide();
        $('#chartView').show();
        $('#tableViewBtn').removeClass('active');
        $('#chartViewBtn').addClass('active');
    } else {
        $('#chartView').hide();
        $('#tableView').show();
        $('#chartViewBtn').removeClass('active');
        $('#tableViewBtn').addClass('active');
    }
}

function initializeChart() {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    const data = {
        labels: [
            @foreach($dailyFlow as $day)
                '{{ $day->transaction_date->format("d/m") }}',
            @endforeach
        ],
        datasets: [{
            label: 'Saldo Acumulado',
            data: [
                @foreach($dailyFlow as $day)
                    {{ $day->cumulative_balance }},
                @endforeach
            ],
            borderColor: '#0d6efd',
            backgroundColor: '#0d6efd20',
            tension: 0.4,
            fill: true
        }, {
            label: 'Entradas',
            data: [
                @foreach($dailyFlow as $day)
                    {{ $day->credits }},
                @endforeach
            ],
            borderColor: '#198754',
            backgroundColor: '#19875420',
            tension: 0.4
        }, {
            label: 'Saídas',
            data: [
                @foreach($dailyFlow as $day)
                    {{ $day->debits }},
                @endforeach
            ],
            borderColor: '#dc3545',
            backgroundColor: '#dc354520',
            tension: 0.4
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}

function filterTable() {
    const searchTerm = $('#searchDates').val().toLowerCase();
    const balanceFilter = $('#filterBalance').val();
    const minAmount = parseFloat($('#minAmount').val()) || 0;
    const maxAmount = parseFloat($('#maxAmount').val()) || Infinity;
    
    $('.cash-flow-row').each(function() {
        const row = $(this);
        const dateText = row.find('td:first').text().toLowerCase();
        const balanceText = row.find('td:last-child').text();
        const isPositive = balanceText.includes('success');
        const amount = parseFloat(row.find('td:nth-child(5)').text().replace(/[^\d,-]/g, '').replace(',', '.'));
        
        let show = true;
        
        if (searchTerm && !dateText.includes(searchTerm)) show = false;
        if (balanceFilter === 'positive' && !isPositive) show = false;
        if (balanceFilter === 'negative' && isPositive) show = false;
        if (amount < minAmount || amount > maxAmount) show = false;
        
        row.toggle(show);
    });
}

function clearTableFilters() {
    $('#searchDates, #minAmount, #maxAmount').val('');
    $('#filterBalance').val('');
    $('.cash-flow-row').show();
}

function exportData(format) {
    const startDate = $('input[name="start_date"]').val();
    const endDate = $('input[name="end_date"]').val();
    const bankAccountId = $('select[name="bank_account_id"]').val();
    
    let url = `{{ route('reports.cash-flow') }}?export=${format}&start_date=${startDate}&end_date=${endDate}`;
    if (bankAccountId) {
        url += `&bank_account_id=${bankAccountId}`;
    }
    
    window.open(url, '_blank');
}

function adjustPeriod() {
    $('input[name="start_date"]').focus();
}
</script>
@endpush
