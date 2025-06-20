@extends('layouts.app')

@section('title', 'Conciliação #' . str_pad($reconciliation->id, 4, '0', STR_PAD_LEFT))

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-balance-scale text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">
            Conciliação #{{ str_pad($reconciliation->id, 4, '0', STR_PAD_LEFT) }}
        </h2>
        <p class="text-muted mb-0">
            <i class="fas fa-university me-2"></i>{{ $reconciliation->bankAccount->name }}
            <span class="mx-2">•</span>
            <i class="fas fa-calendar me-2"></i>{{ $reconciliation->start_date->format('d/m/Y') }} - {{ $reconciliation->end_date->format('d/m/Y') }}
        </p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    @if($reconciliation->status == 'draft')
        <form action="{{ route('reconciliations.process', $reconciliation) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-cog me-2"></i>Processar
            </button>
        </form>
    @elseif($reconciliation->status == 'completed' && $reconciliation->isBalanced())
        <form action="{{ route('reconciliations.approve', $reconciliation) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-thumbs-up me-2"></i>Aprovar
            </button>
        </form>
    @endif
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#shareModal">
        <i class="fas fa-share-alt me-2"></i>Compartilhar
    </button>
    <a href="{{ route('reconciliations.report', $reconciliation) }}" class="btn btn-info">
        <i class="fas fa-file-pdf me-2"></i>Relatório
    </a>
</div>
@endsection

@section('content')
<!-- Status da Conciliação -->
<div class="row mb-4">
    <div class="col-12">
        @php
            $statusConfig = [
                'approved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Aprovada', 'bg' => 'bg-success'],
                'completed' => ['class' => 'info', 'icon' => 'clock', 'text' => 'Completa', 'bg' => 'bg-info'],
                'draft' => ['class' => 'warning', 'icon' => 'edit', 'text' => 'Rascunho', 'bg' => 'bg-warning']
            ];
            $config = $statusConfig[$reconciliation->status] ?? $statusConfig['draft'];
        @endphp
        
        <div class="alert alert-{{ $config['class'] }} border-0 {{ $config['bg'] }} bg-opacity-10">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-{{ $config['icon'] }} fa-2x text-{{ $config['class'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">Status: {{ $config['text'] }}</h5>
                    <p class="mb-0">
                        @if($reconciliation->status == 'approved')
                            Esta conciliação foi aprovada e está finalizada.
                        @elseif($reconciliation->status == 'completed')
                            Esta conciliação foi processada e está aguardando aprovação.
                        @else
                            Esta conciliação ainda está em desenvolvimento.
                        @endif
                    </p>
                </div>
                @if($reconciliation->isBalanced())
                    <div class="ms-3">
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check me-1"></i>Balanceada
                        </span>
                    </div>
                @else
                    <div class="ms-3">
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-exclamation-triangle me-1"></i>Divergente
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cards de Resumo Financeiro -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-arrow-up fa-2x text-success"></i>
                </div>
                <h6 class="text-muted">Saldo Inicial</h6>
                <h3 class="text-success mb-0">
                    R$ {{ number_format($reconciliation->starting_balance, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-plus fa-2x text-info"></i>
                </div>
                <h6 class="text-muted">Total Créditos</h6>
                <h3 class="text-info mb-0">
                    + R$ {{ number_format($summary['total_credits'], 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-minus fa-2x text-danger"></i>
                </div>
                <h6 class="text-muted">Total Débitos</h6>
                <h3 class="text-danger mb-0">
                    - R$ {{ number_format($summary['total_debits'], 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-equals fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted">Saldo Final</h6>
                <h3 class="text-primary mb-0">
                    R$ {{ number_format($reconciliation->ending_balance, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Informações Detalhadas -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>Detalhes da Conciliação
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" style="width: 40%;">
                                    <i class="fas fa-calendar me-2"></i>Período:
                                </td>
                                <td>{{ $reconciliation->start_date->format('d/m/Y') }} a {{ $reconciliation->end_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">
                                    <i class="fas fa-info-circle me-2"></i>Status:
                                </td>
                                <td>
                                    <span class="badge bg-{{ $config['class'] }} fs-6">
                                        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                        {{ $config['text'] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">
                                    <i class="fas fa-user me-2"></i>Criado por:
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px;">
                                            <i class="fas fa-user text-white small"></i>
                                        </div>
                                        {{ $reconciliation->creator->name ?? 'Sistema' }}
                                    </div>
                                </td>
                            </tr>
                            @if($reconciliation->approved_by)
                            <tr>
                                <td class="fw-bold text-muted">
                                    <i class="fas fa-thumbs-up me-2"></i>Aprovado por:
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </div>
                                        {{ $reconciliation->approver->name }}
                                        <br>
                                        <small class="text-muted">{{ $reconciliation->approved_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3">
                                <i class="fas fa-calculator text-primary me-2"></i>Cálculo da Conciliação
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Saldo Inicial:</td>
                                    <td class="text-end">R$ {{ number_format($reconciliation->starting_balance, 2, ',', '.') }}</td>
                                </tr>
                                <tr class="text-success">
                                    <td>+ Créditos:</td>
                                    <td class="text-end">R$ {{ number_format($summary['total_credits'], 2, ',', '.') }}</td>
                                </tr>
                                <tr class="text-danger">
                                    <td>- Débitos:</td>
                                    <td class="text-end">R$ {{ number_format($summary['total_debits'], 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold">Saldo Calculado:</td>
                                    <td class="text-end fw-bold">R$ {{ number_format($reconciliation->calculated_balance ?? 0, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Saldo Final (Extrato):</td>
                                    <td class="text-end fw-bold">R$ {{ number_format($reconciliation->ending_balance, 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top {{ $reconciliation->isBalanced() ? 'text-success' : 'text-danger' }}">
                                    <td class="fw-bold">
                                        @if($reconciliation->isBalanced())
                                            <i class="fas fa-check-circle me-1"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                        @endif
                                        Diferença:
                                    </td>
                                    <td class="text-end fw-bold">
                                        R$ {{ number_format($reconciliation->difference ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if($reconciliation->notes)
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2">
                        <i class="fas fa-sticky-note text-warning me-2"></i>Observações
                    </h6>
                    <p class="mb-0 text-muted">{{ $reconciliation->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>Resumo Estatístico
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="text-primary">{{ $summary['transaction_count'] }}</h3>
                    <p class="text-muted mb-0">Total de Transações</p>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-success">{{ $reconciliation->transactions->where('type', 'credit')->count() }}</h5>
                            <small class="text-muted">Créditos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-danger">{{ $reconciliation->transactions->where('type', 'debit')->count() }}</h5>
                        <small class="text-muted">Débitos</small>
                    </div>
                </div>
                
                <hr>
                
                @if(!$reconciliation->isBalanced())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção!</strong><br>
                    Existe uma diferença de <strong>R$ {{ number_format(abs($reconciliation->difference ?? 0), 2, ',', '.') }}</strong>
                    que precisa ser investigada.
                </div>
                @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Perfeito!</strong><br>
                    Conciliação balanceada corretamente.
                </div>
                @endif
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Transações -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary me-2"></i>Transações da Conciliação
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterTransactions">
                    <i class="fas fa-filter me-2"></i>Filtros
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sort me-2"></i>Ordenar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="sortTransactions('date')">Por Data</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTransactions('amount')">Por Valor</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTransactions('type')">Por Tipo</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="collapse mt-3" id="filterTransactions">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="filterType">
                        <option value="">Todos os tipos</option>
                        <option value="credit">Créditos</option>
                        <option value="debit">Débitos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="filterStatus">
                        <option value="">Todos os status</option>
                        <option value="reconciled">Conciliado</option>
                        <option value="pending">Pendente</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" id="searchTransactions" placeholder="Buscar transações...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-secondary w-100" onclick="clearFilters()">Limpar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="transactionsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-calendar me-2 text-muted"></i>Data
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-file-text me-2 text-muted"></i>Descrição
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-tag me-2 text-muted"></i>Categoria
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-hashtag me-2 text-muted"></i>Referência
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <i class="fas fa-exchange-alt me-2 text-muted"></i>Tipo
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <i class="fas fa-dollar-sign me-2 text-muted"></i>Valor
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <i class="fas fa-check me-2 text-muted"></i>Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliation->transactions as $transaction)
                    <tr class="transaction-row">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="small">
                                    <strong>{{ $transaction->transaction_date->format('d/m/Y') }}</strong>
                                    <br>
                                    <span class="text-muted">{{ $transaction->transaction_date->format('H:i') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <strong>{{ Str::limit($transaction->description, 50) }}</strong>
                                @if(strlen($transaction->description) > 50)
                                    <br>
                                    <small class="text-muted">{{ $transaction->description }}</small>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-secondary">
                                {{ $transaction->category ?? 'Sem categoria' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-muted">{{ $transaction->reference_number ?? '-' }}</code>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }} fs-6">
                                <i class="fas fa-{{ $transaction->type == 'credit' ? 'plus' : 'minus' }} me-1"></i>
                                {{ $transaction->type == 'credit' ? 'Crédito' : 'Débito' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <span class="fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->formatted_amount }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-{{ $transaction->status == 'reconciled' ? 'success' : 'warning' }} fs-6">
                                <i class="fas fa-{{ $transaction->status == 'reconciled' ? 'check' : 'clock' }} me-1"></i>
                                {{ $transaction->status == 'reconciled' ? 'Conciliado' : 'Pendente' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center">
                            <div class="py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                                <p class="text-muted mb-0">Não há transações para este período.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Compartilhamento -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-share-alt me-2"></i>Compartilhar Conciliação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Link da Conciliação</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ url()->current() }}" id="shareUrl" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('#shareUrl')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Enviar por Email</label>
                    <input type="email" class="form-control" placeholder="Digite o email do destinatário">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exportação -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>Exportar Conciliação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <button class="btn btn-outline-danger w-100 mb-2">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-info w-100">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100">
                            <i class="fas fa-file-alt me-2"></i>Texto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Filtros de transações
    $('#filterType, #filterStatus').on('change', function() {
        filterTransactions();
    });
    
    $('#searchTransactions').on('keyup', function() {
        filterTransactions();
    });
});

function filterTransactions() {
    const typeFilter = $('#filterType').val();
    const statusFilter = $('#filterStatus').val();
    const searchFilter = $('#searchTransactions').val().toLowerCase();
    
    $('.transaction-row').each(function() {
        const row = $(this);
        const type = row.find('.badge').text().toLowerCase().includes('crédito') ? 'credit' : 'debit';
        const status = row.find('.badge:last').text().toLowerCase().includes('conciliado') ? 'reconciled' : 'pending';
        const text = row.text().toLowerCase();
        
        let show = true;
        
        if (typeFilter && type !== typeFilter) show = false;
        if (statusFilter && status !== statusFilter) show = false;
        if (searchFilter && !text.includes(searchFilter)) show = false;
        
        row.toggle(show);
    });
}

function clearFilters() {
    $('#filterType, #filterStatus').val('');
    $('#searchTransactions').val('');
    $('.transaction-row').show();
}

function sortTransactions(field) {
    // Implementar ordenação das transações
    console.log('Ordenando por:', field);
}

function copyToClipboard(element) {
    const text = $(element).val();
    navigator.clipboard.writeText(text).then(function() {
        // Feedback visual
        const button = $(element).siblings('button');
        const originalText = button.html();
        button.html('<i class="fas fa-check"></i>');
        setTimeout(() => {
            button.html(originalText);
        }, 2000);
    });
}
</script>
@endpush
