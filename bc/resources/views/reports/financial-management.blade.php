@extends('layouts.app')

@section('title', 'Relatório de Gestão Financeira')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-chart-bar text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Relatório de Gestão Financeira</h2>
        <p class="text-muted mb-0">Visão detalhada do fluxo de caixa, contas a pagar e receber</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimir
    </button>
    <button class="btn btn-primary" onclick="exportReport()">
        <i class="fas fa-download me-2"></i>Exportar PDF
    </button>
</div>
@endsection

@section('content')
<!-- Resumo Executivo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Resumo Executivo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h3 class="text-success mb-1">R$ {{ number_format($cashFlow['to_receive'], 2, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total a Receber</p>
                        <small class="text-muted">{{ $accountsReceivable['pending'] + $accountsReceivable['overdue'] }} contas</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-danger mb-1">R$ {{ number_format($cashFlow['to_pay'], 2, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total a Pagar</p>
                        <small class="text-muted">{{ $accountsPayable['pending'] + $accountsPayable['overdue'] }} contas</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-{{ $cashFlow['balance'] >= 0 ? 'success' : 'warning' }} mb-1">
                            R$ {{ number_format($cashFlow['balance'], 2, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-0">Saldo Projetado</p>
                        <small class="text-muted">{{ $cashFlow['balance'] >= 0 ? 'Positivo' : 'Atenção' }}</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-info mb-1">{{ $totalClients + $totalSuppliers }}</h3>
                        <p class="text-muted mb-0">Clientes/Fornecedores</p>
                        <small class="text-muted">{{ $totalClients }} clientes, {{ $totalSuppliers }} fornecedores</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Distribuição - Contas a Pagar</h6>
            </div>
            <div class="card-body">
                <canvas id="payablesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Distribuição - Contas a Receber</h6>
            </div>
            <div class="card-body">
                <canvas id="receivablesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detalhamento das Contas -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice-dollar text-danger me-2"></i>
                    Contas a Pagar por Status
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Quantidade</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-warning">Pendente</span></td>
                                <td>{{ $accountsPayable['pending'] }}</td>
                                <td>R$ {{ number_format($accountsPayable['pending_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Vencida</span></td>
                                <td>{{ $accountsPayable['overdue'] }}</td>
                                <td>R$ {{ number_format($accountsPayable['overdue_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Paga</span></td>
                                <td>{{ $accountsPayable['paid'] }}</td>
                                <td>R$ {{ number_format($accountsPayable['paid_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr class="table-secondary">
                                <td><strong>Total</strong></td>
                                <td><strong>{{ $accountsPayable['total'] }}</strong></td>
                                <td><strong>R$ {{ number_format($accountsPayable['total_amount'], 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice text-success me-2"></i>
                    Contas a Receber por Status
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Quantidade</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-warning">Pendente</span></td>
                                <td>{{ $accountsReceivable['pending'] }}</td>
                                <td>R$ {{ number_format($accountsReceivable['pending_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Vencida</span></td>
                                <td>{{ $accountsReceivable['overdue'] }}</td>
                                <td>R$ {{ number_format($accountsReceivable['overdue_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Recebida</span></td>
                                <td>{{ $accountsReceivable['received'] }}</td>
                                <td>R$ {{ number_format($accountsReceivable['received_amount'], 2, ',', '.') }}</td>
                            </tr>
                            <tr class="table-secondary">
                                <td><strong>Total</strong></td>
                                <td><strong>{{ $accountsReceivable['total'] }}</strong></td>
                                <td><strong>R$ {{ number_format($accountsReceivable['total_amount'], 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Próximos Vencimentos -->
@if($upcomingPayables->count() > 0 || $upcomingReceivables->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-alt text-warning me-2"></i>
                    Próximos Vencimentos (7 dias)
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($upcomingPayables->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-danger">Contas a Pagar</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fornecedor</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Vencimento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingPayables as $payable)
                                    <tr>
                                        <td>{{ $payable->supplier->name }}</td>
                                        <td>{{ Str::limit($payable->description, 20) }}</td>
                                        <td>{{ $payable->formatted_amount }}</td>
                                        <td>{{ $payable->due_date->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($upcomingReceivables->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-success">Contas a Receber</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Vencimento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingReceivables as $receivable)
                                    <tr>
                                        <td>{{ $receivable->client->name }}</td>
                                        <td>{{ Str::limit($receivable->description, 20) }}</td>
                                        <td>{{ $receivable->formatted_amount }}</td>
                                        <td>{{ $receivable->due_date->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Contas Vencidas -->
@if($overduePayables->count() > 0 || $overdueReceivables->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Contas Vencidas - Ação Urgente Necessária
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($overduePayables->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-danger">Contas a Pagar Vencidas ({{ $overduePayables->count() }})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fornecedor</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Venceu em</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overduePayables as $payable)
                                    <tr>
                                        <td>{{ $payable->supplier->name }}</td>
                                        <td>{{ Str::limit($payable->description, 15) }}</td>
                                        <td>{{ $payable->formatted_amount }}</td>
                                        <td>{{ $payable->due_date->format('d/m/Y') }}</td>
                                        <td class="text-danger">{{ abs($payable->days_until_due) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($overdueReceivables->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-danger">Contas a Receber Vencidas ({{ $overdueReceivables->count() }})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Venceu em</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueReceivables as $receivable)
                                    <tr>
                                        <td>{{ $receivable->client->name }}</td>
                                        <td>{{ Str::limit($receivable->description, 15) }}</td>
                                        <td>{{ $receivable->formatted_amount }}</td>
                                        <td>{{ $receivable->due_date->format('d/m/Y') }}</td>
                                        <td class="text-danger">{{ abs($receivable->days_until_due) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Contas a Pagar
const payablesCtx = document.getElementById('payablesChart').getContext('2d');
new Chart(payablesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pendente', 'Vencida', 'Paga'],
        datasets: [{
            data: [{{ $accountsPayable['pending'] }}, {{ $accountsPayable['overdue'] }}, {{ $accountsPayable['paid'] }}],
            backgroundColor: ['#ffc107', '#dc3545', '#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de Contas a Receber
const receivablesCtx = document.getElementById('receivablesChart').getContext('2d');
new Chart(receivablesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pendente', 'Vencida', 'Recebida'],
        datasets: [{
            data: [{{ $accountsReceivable['pending'] }}, {{ $accountsReceivable['overdue'] }}, {{ $accountsReceivable['received'] }}],
            backgroundColor: ['#ffc107', '#dc3545', '#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function exportReport() {
    // Implementar exportação para PDF
    alert('Funcionalidade de exportação será implementada em breve!');
}
</script>
@endsection
