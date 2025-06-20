@extends('layouts.app')

@section('title', 'Gestão de Contas')

@section('header')
<div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <i class="fas fa-chart-line text-primary me-3" style="font-size: 2rem;"></i>
        <div>
            <h2 class="mb-0">Gestão de Contas</h2>
            <p class="text-muted mb-0">Controle total das suas contas bancárias e cartões</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="compareAccounts()">
            <i class="fas fa-balance-scale me-2"></i>Comparar Contas
        </button>
        <a href="{{ route('account-management.transfer.form') }}" class="btn btn-outline-success">
            <i class="fas fa-exchange-alt me-2"></i>Transferência
        </a>
        <a href="{{ route('account-management.transfer.history') }}" class="btn btn-outline-info">
            <i class="fas fa-history me-2"></i>Histórico
        </a>
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nova Conta
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Resumo Geral -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-wallet text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-primary">{{ number_format($totalBalance, 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Saldo Total</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-arrow-up text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-success">{{ number_format($totalCredit, 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Entradas</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-arrow-down text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-danger">{{ number_format($totalDebit, 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Saídas</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-university text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-info">{{ $accounts->count() }}</h3>
                <p class="text-muted mb-0">Total Contas</p>
            </div>
        </div>
    </div>
</div>

<!-- Contas por Tipo -->
<div class="row mb-4">
    @foreach($accountTypes as $typeData)
    <div class="col-lg-6 col-xl-3 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h6 class="mb-0">
                    @switch($typeData['type'])
                        @case('checking')
                            <i class="fas fa-university text-primary me-2"></i>
                            @break
                        @case('savings')
                            <i class="fas fa-piggy-bank text-success me-2"></i>
                            @break
                        @case('credit_card')
                            <i class="fas fa-credit-card text-warning me-2"></i>
                            @break
                        @case('investment')
                            <i class="fas fa-chart-line text-info me-2"></i>
                            @break
                    @endswitch
                    {{ $typeData['type_name'] }}
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">{{ $typeData['count'] }} contas</span>
                    <strong class="text-primary">R$ {{ number_format($typeData['total_balance'], 2, ',', '.') }}</strong>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $totalBalance > 0 ? ($typeData['total_balance'] / $totalBalance * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Lista de Contas -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Todas as Contas
                </h5>
            </div>
            <div class="card-body p-0">
                @if($accounts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Conta</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Saldo</th>
                                    <th class="text-center">Transações</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input account-checkbox" 
                                               value="{{ $account->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @switch($account->type)
                                                    @case('checking')
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                            <i class="fas fa-university text-primary"></i>
                                                        </div>
                                                        @break
                                                    @case('savings')
                                                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                            <i class="fas fa-piggy-bank text-success"></i>
                                                        </div>
                                                        @break
                                                    @case('credit_card')
                                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                            <i class="fas fa-credit-card text-warning"></i>
                                                        </div>
                                                        @break
                                                    @case('investment')
                                                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                            <i class="fas fa-chart-line text-info"></i>
                                                        </div>
                                                        @break
                                                @endswitch
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $account->name }}</h6>
                                                <small class="text-muted">{{ $account->bank_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $account->type_name }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="{{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($account->balance, 2, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $account->transactions_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($account->active)
                                            <span class="badge bg-success">Ativa</span>
                                        @else
                                            <span class="badge bg-secondary">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('account-management.show', $account) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bank-accounts.edit', $account) }}" 
                                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="quickTransfer({{ $account->id }})" title="Transferir">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-university text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Nenhuma conta cadastrada</h5>
                        <p class="text-muted">Comece criando sua primeira conta bancária</p>
                        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Criar Primeira Conta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Contas com Maior Movimentação -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0">
                    <i class="fas fa-fire text-danger me-2"></i>Mais Movimentadas
                </h6>
            </div>
            <div class="card-body">
                @forelse($topAccounts as $account)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            @switch($account->type)
                                @case('checking')
                                    <i class="fas fa-university text-primary"></i>
                                    @break
                                @case('savings')
                                    <i class="fas fa-piggy-bank text-success"></i>
                                    @break
                                @case('credit_card')
                                    <i class="fas fa-credit-card text-warning"></i>
                                    @break
                                @case('investment')
                                    <i class="fas fa-chart-line text-info"></i>
                                    @break
                            @endswitch
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $account->name }}</h6>
                            <small class="text-muted">{{ $account->transactions_count }} transações</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark">{{ $account->transactions_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Nenhuma movimentação encontrada</p>
                @endforelse
            </div>
        </div>
        
        <!-- Atividade Recente -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0">
                    <i class="fas fa-clock text-info me-2"></i>Atividade Recente
                </h6>
            </div>
            <div class="card-body">
                @forelse($recentTransactions as $transaction)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }} bg-opacity-10 rounded-circle p-2">
                                <i class="fas fa-{{ $transaction->type == 'credit' ? 'arrow-up' : 'arrow-down' }} 
                                   text-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ Str::limit($transaction->description, 20) }}</h6>
                            <small class="text-muted">{{ $transaction->bankAccount->name }}</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                {{ $transaction->type == 'credit' ? '+' : '-' }}R$ {{ number_format(abs($transaction->amount), 2, ',', '.') }}
                            </strong>
                            <br>
                            <small class="text-muted">{{ $transaction->transaction_date->format('d/m') }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Nenhuma transação recente</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal de Comparação -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comparar Contas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="compareForm" action="{{ route('account-management.compare') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Selecione as contas para comparar:</label>
                        <div id="accountsForComparison" class="mt-2">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitComparison()">Comparar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Selecionar todas as contas
    $('#selectAll').change(function() {
        $('.account-checkbox').prop('checked', $(this).is(':checked'));
    });
    
    // Verificar se todas estão selecionadas
    $('.account-checkbox').change(function() {
        if (!$(this).is(':checked')) {
            $('#selectAll').prop('checked', false);
        } else if ($('.account-checkbox:checked').length === $('.account-checkbox').length) {
            $('#selectAll').prop('checked', true);
        }
    });
});

function compareAccounts() {
    const selectedAccounts = $('.account-checkbox:checked');
    
    if (selectedAccounts.length < 2) {
        alert('Selecione pelo menos 2 contas para comparar.');
        return;
    }
    
    // Preencher modal com contas selecionadas
    let html = '';
    selectedAccounts.each(function() {
        const accountId = $(this).val();
        const accountRow = $(this).closest('tr');
        const accountName = accountRow.find('h6').text();
        
        html += `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="accounts[]" value="${accountId}" checked>
                <label class="form-check-label">${accountName}</label>
            </div>
        `;
    });
    
    $('#accountsForComparison').html(html);
    $('#compareModal').modal('show');
}

function submitComparison() {
    $('#compareForm').submit();
}

function quickTransfer(accountId) {
    // Implementar modal de transferência rápida
    alert('Funcionalidade de transferência será implementada em breve');
}
</script>
@endpush
