@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Conciliação Bancária')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-chart-line text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Dashboard Financeiro</h2>
        <p class="text-muted mb-0">Visão geral das suas movimentações e conciliações bancárias</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" onclick="refreshDashboard()">
        <i class="fas fa-sync-alt me-2"></i>Atualizar
    </button>
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-2"></i>Exportar
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="exportDashboard('pdf')">
                <i class="fas fa-file-pdf me-2"></i>Relatório PDF
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="exportDashboard('excel')">
                <i class="fas fa-file-excel me-2"></i>Planilha Excel
            </a></li>
        </ul>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
        <i class="fas fa-bolt me-2"></i>Ações Rápidas
    </button>
</div>
@endsection

@section('content')
<!-- Cards de Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">
                            <i class="fas fa-university me-2"></i>Contas Ativas
                        </h6>
                        <h2 class="text-primary mb-0">{{ $stats['active_accounts'] ?? 0 }}</h2>
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>100% operacionais
                        </small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class="fas fa-university fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">
                            <i class="fas fa-clock me-2"></i>Transações Pendentes
                        </h6>
                        <h2 class="text-warning mb-0">{{ $stats['pending_transactions'] ?? 0 }}</h2>
                        <small class="text-muted">
                            <i class="fas fa-hourglass-half me-1"></i>Aguardando processamento
                        </small>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">
                            <i class="fas fa-balance-scale me-2"></i>Conciliações do Mês
                        </h6>
                        <h2 class="text-success mb-0">{{ $stats['month_reconciliations'] ?? 0 }}</h2>
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>Processadas com sucesso
                        </small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-3">
                        <i class="fas fa-balance-scale fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">
                            <i class="fas fa-dollar-sign me-2"></i>Saldo Total
                        </h6>
                        <h2 class="text-info mb-0">R$ {{ number_format($stats['total_balance'] ?? 0, 2, ',', '.') }}</h2>
                        <small class="text-info">
                            <i class="fas fa-chart-line me-1"></i>Patrimônio total
                        </small>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-3">
                        <i class="fas fa-wallet fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Análises -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area text-primary me-2"></i>Movimentação Financeira
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" onclick="changeChartPeriod('7')">7 dias</button>
                        <button class="btn btn-outline-secondary" onclick="changeChartPeriod('30')">30 dias</button>
                        <button class="btn btn-outline-secondary" onclick="changeChartPeriod('90')">90 dias</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financialChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie text-secondary me-2"></i>Distribuição por Categoria
                </h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" width="300" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Seção Principal com Tabs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#transactions-tab">
                    <i class="fas fa-exchange-alt me-2"></i>Últimas Transações
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reconciliations-tab">
                    <i class="fas fa-balance-scale me-2"></i>Conciliações Recentes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alertas
                    @if(isset($alerts) && count($alerts) > 0)
                        <span class="badge bg-danger ms-1">{{ count($alerts) }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#summary-tab">
                    <i class="fas fa-chart-bar me-2"></i>Resumo Mensal
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content">
            <!-- Tab de Transações -->
            <div class="tab-pane fade show active" id="transactions-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Últimas 10 Transações</h6>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Ver Todas
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="fas fa-calendar me-2 text-muted"></i>Data
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-university me-2 text-muted"></i>Conta
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-file-text me-2 text-muted"></i>Descrição
                                </th>
                                <th class="border-0 text-end">
                                    <i class="fas fa-dollar-sign me-2 text-muted"></i>Valor
                                </th>
                                <th class="border-0 text-center">
                                    <i class="fas fa-info-circle me-2 text-muted"></i>Status
                                </th>
                                <th class="border-0 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions ?? [] as $transaction)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-2">
                                            <i class="fas fa-calendar-day text-primary small"></i>
                                        </div>
                                        <div class="small">
                                            <strong>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : 'N/A' }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $transaction->transaction_date ? $transaction->transaction_date->format('H:i') : '00:00' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <strong>{{ $transaction->bankAccount->name ?? 'Conta não informada' }}</strong>
                                        <br>
                                        <span class="text-muted">{{ $transaction->bankAccount->bank_name ?? 'Banco não informado' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <strong>{{ Str::limit($transaction->description ?? 'Sem descrição', 40) }}</strong>
                                        @if($transaction->category)
                                            <br>
                                            <span class="badge bg-secondary">{{ $transaction->category }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold {{ ($transaction->type ?? 'debit') == 'credit' ? 'text-success' : 'text-danger' }}">
                                        <i class="fas fa-{{ ($transaction->type ?? 'debit') == 'credit' ? 'plus' : 'minus' }}-circle me-1"></i>
                                        {{ $transaction->formatted_amount ?? 'R$ 0,00' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ ($transaction->status ?? 'pending') == 'reconciled' ? 'success' : (($transaction->status ?? 'pending') == 'pending' ? 'warning' : 'secondary') }} fs-6">
                                        <i class="fas fa-{{ ($transaction->status ?? 'pending') == 'reconciled' ? 'check' : 'clock' }} me-1"></i>
                                        {{ ($transaction->status ?? 'pending') == 'reconciled' ? 'Conciliado' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="btn btn-outline-primary" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(($transaction->status ?? 'pending') != 'reconciled')
                                            <a href="{{ route('transactions.edit', $transaction) }}" 
                                               class="btn btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="py-3">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Nenhuma transação encontrada</h6>
                                        <p class="text-muted mb-0">Não há transações recentes para exibir.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab de Conciliações -->
            <div class="tab-pane fade" id="reconciliations-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Conciliações Recentes</h6>
                    <a href="{{ route('reconciliations.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-balance-scale me-2"></i>Ver Todas
                    </a>
                </div>
                
                @if(isset($recentReconciliations) && $recentReconciliations->count() > 0)
                <div class="row">
                    @foreach($recentReconciliations as $reconciliation)
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $reconciliation->bankAccount->name }}</h6>
                                    @php
                                        $statusConfig = [
                                            'approved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Aprovada'],
                                            'completed' => ['class' => 'info', 'icon' => 'clock', 'text' => 'Completa'],
                                            'draft' => ['class' => 'warning', 'icon' => 'edit', 'text' => 'Rascunho']
                                        ];
                                        $config = $statusConfig[$reconciliation->status] ?? $statusConfig['draft'];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }}">
                                        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                        {{ $config['text'] }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ $reconciliation->start_date->format('d/m/Y') }} - {{ $reconciliation->end_date->format('d/m/Y') }}
                                </small>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <small class="text-muted">Saldo Final</small>
                                        <div class="fw-bold text-primary">
                                            R$ {{ number_format($reconciliation->ending_balance, 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Diferença</small>
                                        <div class="fw-bold {{ $reconciliation->isBalanced() ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format(abs($reconciliation->difference ?? 0), 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="{{ route('reconciliations.show', $reconciliation) }}" class="btn btn-sm btn-outline-primary">
                                        Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Nenhuma conciliação recente</h6>
                    <p class="text-muted mb-3">Não há conciliações recentes para exibir.</p>
                    <a href="{{ route('reconciliations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Conciliação
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Tab de Alertas -->
            <div class="tab-pane fade" id="alerts-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Alertas e Notificações</h6>
                    <button class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-2"></i>Marcar como Lidas
                    </button>
                </div>
                
                @if(isset($alerts) && count($alerts) > 0)
                <div class="list-group list-group-flush">
                    @foreach($alerts as $alert)
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex align-items-start">
                            <div class="bg-{{ $alert['type'] == 'warning' ? 'warning' : ($alert['type'] == 'error' ? 'danger' : 'info') }} bg-opacity-25 rounded p-2 me-3">
                                <i class="fas fa-{{ $alert['type'] == 'warning' ? 'exclamation-triangle' : ($alert['type'] == 'error' ? 'times-circle' : 'info-circle') }} text-{{ $alert['type'] == 'warning' ? 'warning' : ($alert['type'] == 'error' ? 'danger' : 'info') }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $alert['title'] }}</h6>
                                <p class="text-muted mb-1">{{ $alert['message'] }}</p>
                                <small class="text-muted">Agora mesmo</small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='{{ $alert->action ?? '#' }}'">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h6 class="text-muted">Tudo em ordem!</h6>
                    <p class="text-muted mb-0">Não há alertas pendentes no momento.</p>
                </div>
                @endif
            </div>
            
            <!-- Tab de Resumo Mensal -->
            <div class="tab-pane fade" id="summary-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Resumo Mensal - {{ now()->format('F Y') }}</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="changeSummaryMonth(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="changeSummaryMonth(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 bg-success bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                <h6 class="text-muted">Total de Entradas</h6>
                                <h4 class="text-success">R$ {{ number_format($monthlySummary['credits'] ?? 0, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 bg-danger bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                                <h6 class="text-muted">Total de Saídas</h6>
                                <h4 class="text-danger">R$ {{ number_format($monthlySummary['debits'] ?? 0, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 bg-primary bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-balance-scale fa-2x text-primary mb-2"></i>
                                <h6 class="text-muted">Saldo Líquido</h6>
                                @php $netBalance = ($monthlySummary['credits'] ?? 0) - ($monthlySummary['debits'] ?? 0); @endphp
                                <h4 class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($netBalance, 2, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(isset($monthlyStats))
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">Principais Categorias (Saídas)</h6>
                        @foreach($monthlyStats['top_categories'] ?? [] as $category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ $category['name'] }}</span>
                            <span class="fw-bold text-danger">R$ {{ number_format($category['total'], 2, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Estatísticas do Período</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total de Transações</span>
                            <span class="fw-bold">{{ $monthlyStats['total_transactions'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Média por Dia</span>
                            <span class="fw-bold">{{ number_format(($monthlyStats['daily_average'] ?? 0), 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Conciliações Realizadas</span>
                            <span class="fw-bold">{{ $monthlyStats['reconciliations_count'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ações Rápidas -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('transactions.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus mb-2 d-block"></i>
                            Nova Transação
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('reconciliations.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-balance-scale mb-2 d-block"></i>
                            Nova Conciliação
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('imports.create') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-upload mb-2 d-block"></i>
                            Importar Extrato
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-chart-bar mb-2 d-block"></i>
                            Gerar Relatório
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('categories.create') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-tag mb-2 d-block"></i>
                            Nova Categoria
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('bank-accounts.create') }}" class="btn btn-outline-dark w-100">
                            <i class="fas fa-university mb-2 d-block"></i>
                            Nova Conta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast para loading -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="loadingToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-spinner fa-spin me-2"></i>
            <strong class="me-auto">Atualizando</strong>
        </div>
        <div class="toast-body">
            Carregando dados do dashboard...
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-color: transparent;
}

.list-group-item {
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

#financialChart, #categoryChart {
    max-height: 300px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Inicializar gráficos
    initializeFinancialChart();
    initializeCategoryChart();
    
    // Auto-refresh a cada 5 minutos
    setInterval(function() {
        refreshDashboard();
    }, 5 * 60 * 1000);
});

function initializeFinancialChart() {
    const ctx = document.getElementById('financialChart').getContext('2d');
    
    // Dados reais do backend
    const chartData = @json($chartData['monthly'] ?? ['labels' => [], 'datasets' => []]);
    
    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Período'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Valor (R$)'
                    },
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

function initializeCategoryChart() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Dados reais do backend
    const chartData = @json($chartData['categories'] ?? ['labels' => [], 'datasets' => []]);
    
    if (chartData.labels.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText('Nenhum dado disponível', 150, 150);
        return;
    }
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const dataset = data.datasets[0];
                                    const value = dataset.data[i];
                                    const total = dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor || '#fff',
                                        lineWidth: 2,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function refreshDashboard() {
    // Mostrar loading
    const loadingToast = new bootstrap.Toast(document.getElementById('loadingToast'));
    if (loadingToast) {
        loadingToast.show();
    }
    
    $.ajax({
        url: window.location.href,
        type: 'GET',
        data: { ajax: 1 },
        success: function(response) {
            // Atualizar estatísticas
            if (response.stats) {
                updateStats(response.stats);
            }
            
            // Recarregar gráficos
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Erro ao atualizar dashboard:', error);
            
            // Mostrar erro
            const errorAlert = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erro ao atualizar dashboard. Tente novamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('.main-content .container-fluid').prepend(errorAlert);
        }
    });
}

function updateStats(stats) {
    // Atualizar valores nos cards
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            if (key.includes('balance') || key.includes('total')) {
                element.textContent = formatCurrency(stats[key]);
            } else {
                element.textContent = stats[key];
            }
        }
    });
}

function changeChartPeriod(days) {
    // Destacar botão ativo
    $('.btn-group .btn').removeClass('active');
    $(`.btn-group .btn[onclick="changeChartPeriod('${days}')"]`).addClass('active');
    
    // Carregar dados para o período
    $.ajax({
        url: '{{ route("dashboard.chart-data") }}',
        type: 'GET',
        data: { 
            type: 'monthly',
            period: days 
        },
        success: function(response) {
            // Atualizar gráfico
            updateFinancialChart(response);
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar dados do gráfico:', error);
        }
    });
}

function updateFinancialChart(data) {
    const chart = Chart.getChart('financialChart');
    if (chart) {
        chart.data = data;
        chart.update();
    }
}

function exportDashboard(format) {
    // Redirecionar para a nova rota de exportação
    const url = '/bc/dashboard/export/' + format;
    window.open(url, '_blank');
}

// Função para formatar moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        });
}

function initializeCategoryChart() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Alimentação', 'Transporte', 'Educação', 'Saúde', 'Outros'],
            datasets: [{
                data: [30, 25, 20, 15, 10],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
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
        });
}

function refreshDashboard() {
    location.reload();
}

function changeChartPeriod(days) {
    // Implementar mudança de período do gráfico
    console.log('Mudando período para:', days, 'dias');
    $('.btn-group .btn').removeClass('active');
    event.target.classList.add('active');
}

function changeSummaryMonth(direction) {
    // Implementar mudança de mês do resumo
    console.log('Mudando mês:', direction);
}

function markAllAsRead() {
    // Implementar marcação de alertas como lidos
    $('#alerts-tab .list-group-item').fadeOut();
}

function dismissAlert(id) {
    // Implementar dismissão de alerta específico
    console.log('Dismissing alert:', id);
}
</script>
@endpush