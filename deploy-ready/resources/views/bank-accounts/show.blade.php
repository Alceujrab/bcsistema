@extends('layouts.app')

@section('title', $bankAccount->name)

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-university text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">{{ $bankAccount->name }}</h2>
        <p class="text-muted mb-0">{{ $bankAccount->bank_name }} - {{ ucfirst($bankAccount->type) }}</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#transactionModal">
        <i class="fas fa-plus me-2"></i>Nova Transação
    </button>
    <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Editar
    </a>
    <a href="{{ route('bank-accounts.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>
@endsection

@section('content')
<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Saldo Atual</h6>
                        <h3 class="mb-0">R$ {{ number_format($bankAccount->balance, 2, ',', '.') }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-wallet fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Total Créditos</h6>
                        <h3 class="mb-0">R$ {{ number_format($stats['total_credit'], 2, ',', '.') }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-arrow-up fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Total Débitos</h6>
                        <h3 class="mb-0">R$ {{ number_format($stats['total_debit'], 2, ',', '.') }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-arrow-down fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Transações</h6>
                        <h3 class="mb-0">{{ $stats['reconciled_count'] + $stats['pending_count'] }}</h3>
                        <small class="opacity-75">{{ $stats['pending_count'] }} pendentes</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-list fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações da Conta -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informações da Conta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%" class="text-muted">
                                    <i class="fas fa-university me-2"></i>Banco:
                                </th>
                                <td class="fw-bold">{{ $bankAccount->bank_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-hashtag me-2"></i>Conta:
                                </th>
                                <td>{{ $bankAccount->account_number }}</td>
                            </tr>
                            @if($bankAccount->agency)
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>Agência:
                                </th>
                                <td>{{ $bankAccount->agency }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-tags me-2"></i>Tipo:
                                </th>
                                <td>
                                    @switch($bankAccount->type)
                                        @case('checking')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-university me-1"></i>Conta Corrente
                                            </span>
                                            @break
                                        @case('savings')
                                            <span class="badge bg-success">
                                                <i class="fas fa-piggy-bank me-1"></i>Poupança
                                            </span>
                                            @break
                                        @case('credit_card')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-credit-card me-1"></i>Cartão de Crédito
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($bankAccount->type) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%" class="text-muted">
                                    <i class="fas fa-money-bill-wave me-2"></i>Saldo Inicial:
                                </th>
                                <td class="fw-bold">R$ {{ number_format($bankAccount->initial_balance, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-calendar me-2"></i>Criada em:
                                </th>
                                <td>{{ $bankAccount->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-clock me-2"></i>Última Atualização:
                                </th>
                                <td>{{ $bankAccount->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-toggle-on me-2"></i>Status:
                                </th>
                                <td>
                                    @if($bankAccount->status == 'active')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Ativa
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Inativa
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($bankAccount->description)
                <div class="mt-3">
                    <h6 class="text-muted">
                        <i class="fas fa-sticky-note me-2"></i>Observações:
                    </h6>
                    <p class="mb-0">{{ $bankAccount->description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Transações Recentes -->
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Transações Recentes
                </h5>
                <a href="{{ route('transactions.index', ['bank_account' => $bankAccount->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>Ver Todas
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($transaction->description, 30) }}</div>
                                                @if($transaction->reference)
                                                    <small class="text-muted">Ref: {{ $transaction->reference }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($transaction->category) && is_object($transaction->category) && !empty($transaction->category->name))
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-tag me-1"></i>{{ $transaction->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Não categorizada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type == 'credit')
                                            <span class="badge bg-success">
                                                <i class="fas fa-arrow-up me-1"></i>Crédito
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-arrow-down me-1"></i>Débito
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type == 'credit' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->is_reconciled)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Conciliada
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pendente
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($recentTransactions->count() >= 10)
                    <div class="card-footer text-center">
                        <a href="{{ route('transactions.index', ['bank_account' => $bankAccount->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Ver Todas as Transações
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma transação encontrada para esta conta.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transactionModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Primeira Transação
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Ações Rápidas -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transactionModal">
                        <i class="fas fa-plus me-2"></i>Nova Transação
                    </button>
                    <a href="{{ route('transactions.index', ['bank_account' => $bankAccount->id]) }}" class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i>Ver Transações
                    </a>
                    <a href="{{ route('reconciliations.create', ['bank_account' => $bankAccount->id]) }}" class="btn btn-outline-warning">
                        <i class="fas fa-balance-scale me-2"></i>Nova Conciliação
                    </a>
                    <a href="{{ route('reports.index', ['bank_account' => $bankAccount->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Resumo Mensal -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Resumo do Mês
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-chart-line text-primary fa-2x mb-2"></i>
                            <h5 class="mb-0">{{ $monthlyStats['transactions_count'] ?? 0 }}</h5>
                            <small class="text-muted">Transações este mês</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-success bg-opacity-25 rounded">
                            <i class="fas fa-arrow-up text-success fa-lg mb-2"></i>
                            <h6 class="mb-0 text-success">
                                R$ {{ number_format($monthlyStats['total_credit'] ?? 0, 2, ',', '.') }}
                            </h6>
                            <small class="text-muted">Créditos</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-danger bg-opacity-25 rounded">
                            <i class="fas fa-arrow-down text-danger fa-lg mb-2"></i>
                            <h6 class="mb-0 text-danger">
                                R$ {{ number_format($monthlyStats['total_debit'] ?? 0, 2, ',', '.') }}
                            </h6>
                            <small class="text-muted">Débitos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Categorias Mais Usadas -->
        @if(isset($topCategories) && $topCategories->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Categorias Mais Usadas
                </h6>
            </div>
            <div class="card-body">
                @foreach($topCategories as $category)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tag text-secondary me-2"></i>
                        <div>
                            <div class="fw-bold">{{ $category->name }}</div>
                            <small class="text-muted">{{ $category->transactions_count }} transações</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold">
                            R$ {{ number_format($category->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Nova Transação -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle text-success me-2"></i>Nova Transação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" name="bank_account_id" value="{{ $bankAccount->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label fw-bold">
                                <i class="fas fa-calendar me-2"></i>Data
                            </label>
                            <input type="date" name="transaction_date" id="transaction_date" 
                                   class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-bold">
                                <i class="fas fa-exchange-alt me-2"></i>Tipo
                            </label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="credit">
                                    <i class="fas fa-arrow-up me-2"></i>Crédito (+)
                                </option>
                                <option value="debit">
                                    <i class="fas fa-arrow-down me-2"></i>Débito (-)
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-2"></i>Valor
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control" step="0.01" placeholder="0,00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-bold">
                                <i class="fas fa-tag me-2"></i>Categoria
                            </label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-file-alt me-2"></i>Descrição
                        </label>
                        <input type="text" name="description" id="description" 
                               class="form-control" placeholder="Descrição da transação" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">
                            <i class="fas fa-hashtag me-2"></i>Referência
                        </label>
                        <input type="text" name="reference" id="reference" 
                               class="form-control" placeholder="Número do documento, referência, etc.">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveTransaction">
                    <i class="fas fa-save me-2"></i>Salvar Transação
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de Nova Transação
    const transactionModal = new bootstrap.Modal(document.getElementById('transactionModal'));
    const transactionForm = document.getElementById('transactionForm');
    
    // Salvar transação
    document.getElementById('saveTransaction').addEventListener('click', function() {
        const formData = new FormData(transactionForm);
        
        // Validação básica
        if (!formData.get('transaction_date') || !formData.get('type') || !formData.get('amount') || !formData.get('description')) {
            showToast('Por favor, preencha todos os campos obrigatórios.', 'warning');
            return;
        }
        
        // Simular salvamento (em produção, fazer requisição AJAX)
        showToast('Transação salva com sucesso!', 'success');
        transactionModal.hide();
        transactionForm.reset();
        
        // Recarregar página após 1 segundo
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
    
    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl+N para nova transação
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            transactionModal.show();
        }
        
        // Ctrl+E para editar
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            window.location.href = "{{ route('bank-accounts.edit', $bankAccount) }}";
        }
    });
    
    // Tooltip em elementos com data-bs-toggle="tooltip"
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Toast de feedback
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'warning' ? 'exclamation-triangle' : 'info'}-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const container = document.querySelector('.toast-container') || createToastContainer();
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
        return container;
    }
});
</script>
@endpush
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Últimas Transações</h5>
                <a href="{{ route('transactions.index', ['bank_account_id' => $bankAccount->id]) }}" class="btn btn-sm 
btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankAccount->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction) }}">
                                        {{ Str::limit($transaction->description, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $transaction->category ?? 'Sem categoria' }}
                                    </span>
                                </td>
                                <td class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->formatted_amount }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status == 'reconciled' ? 'success' : 'warning' }}">
                                        {{ $transaction->status == 'reconciled' ? 'Conciliado' : 'Pendente' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Nenhuma transação registrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Informações da Conta</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Banco:</th>
                        <td>{{ $bankAccount->bank_name }}</td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td>{{ $bankAccount->type_name }}</td>
                    </tr>
                    <tr>
                        <th>Número:</th>
                        <td>{{ $bankAccount->account_number }}</td>
                    </tr>
                    @if($bankAccount->agency)
                    <tr>
                        <th>Agência:</th>
                        <td>{{ $bankAccount->agency }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-{{ $bankAccount->active ? 'success' : 'danger' }}">
                                {{ $bankAccount->active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Criada em:</th>
                        <td>{{ $bankAccount->created_at->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('transactions.create') }}?bank_account_id={{ $bankAccount->id }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i> Nova Transação
                    </a>
                    <a href="{{ route('imports.create') }}?bank_account_id={{ $bankAccount->id }}" class="btn btn-primary">
                        <i class="fas fa-file-import me-2"></i> Importar Extrato
                    </a>
                    <a href="{{ route('reconciliations.create') }}?bank_account_id={{ $bankAccount->id }}" class="btn btn-info">
                        <i class="fas fa-balance-scale me-2"></i> Nova Conciliação
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
