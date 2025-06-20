@extends('layouts.app')

@section('title', 'Detalhes da Transação')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-receipt text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Detalhes da Transação</h2>
        <p class="text-muted mb-0">Visualizar informações completas da transação</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
    @if(isset($transaction->status) && $transaction->status !== 'reconciled')
        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
    @endif
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-cog me-2"></i>Ações
        </button>
        <ul class="dropdown-menu">
            @if(isset($transaction->status) && $transaction->status !== 'reconciled')
                <li>
                    <a class="dropdown-item" href="#" onclick="reconcileTransaction()">
                        <i class="fas fa-check-circle me-2 text-success"></i>Conciliar
                    </a>
                </li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="#" onclick="duplicateTransaction()">
                    <i class="fas fa-copy me-2 text-info"></i>Duplicar
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="exportTransaction()">
                    <i class="fas fa-download me-2 text-secondary"></i>Exportar
                </a>
            </li>
            @if(isset($transaction->status) && $transaction->status !== 'reconciled')
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="deleteTransaction()">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
@endsection
@section('content')
<div class="row">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>Informações da Transação
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-calendar me-2"></i>DATA DA TRANSAÇÃO
                        </label>
                        <div class="form-control-plaintext fs-5">
                            {{ isset($transaction->transaction_date) && $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y H:i') : 'Não informado' }}
                        </div>
                        <small class="text-muted">
                            {{ isset($transaction->transaction_date) && $transaction->transaction_date ? $transaction->transaction_date->diffForHumans() : '' }}
                        </small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-dollar-sign me-2"></i>VALOR
                        </label>
                        <div class="form-control-plaintext">
                            <span class="fs-3 fw-bold {{ (isset($transaction->type) && $transaction->type === 'credit') ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ (isset($transaction->type) && $transaction->type === 'credit') ? 'plus' : 'minus' }}-circle me-2"></i>
                                {{ isset($transaction->formatted_amount) ? $transaction->formatted_amount : 'R$ 0,00' }}
                            </span>
                        </div>
                        <small class="text-muted">
                            Tipo: {{ (isset($transaction->type) && $transaction->type === 'credit') ? 'Crédito' : 'Débito' }}
                        </small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-file-text me-2"></i>DESCRIÇÃO
                        </label>
                        <div class="form-control-plaintext fs-6 p-3 bg-light rounded">
                            {{ $transaction->description ?? 'Sem descrição' }}
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-university me-2"></i>CONTA BANCÁRIA
                        </label>
                        <div class="form-control-plaintext">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-university text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ isset($transaction->bankAccount->name) ? $transaction->bankAccount->name : 'Conta não informada' }}</div>
                                    <small class="text-muted">{{ isset($transaction->bankAccount->bank_name) ? $transaction->bankAccount->bank_name : 'Banco não informado' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-tag me-2"></i>CATEGORIA
                        </label>
                        <div class="form-control-plaintext">
                            @if(isset($transaction->category) && is_object($transaction->category) && !empty($transaction->category->name))
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    <i class="fas fa-folder me-2"></i>{{ $transaction->category->name }}
                                </span>
                            @else
                                <span class="text-muted">Não categorizada</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if(isset($transaction->reference_number) && $transaction->reference_number)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small fw-bold">
                            <i class="fas fa-hashtag me-2"></i>NÚMERO DE REFERÊNCIA
                        </label>
                        <div class="form-control-plaintext">
                            <code class="fs-6">{{ $transaction->reference_number }}</code>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Status e Ações -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-flag text-secondary me-2"></i>Status da Transação
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @php
                        $statusConfig = [
                            'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pendente'],
                            'reconciled' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Conciliada'],
                            'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelada'],
                        ];
                        $transactionStatus = isset($transaction->status) ? $transaction->status : 'pending';
                        $status = $statusConfig[$transactionStatus] ?? $statusConfig['pending'];
                    @endphp
                    
                    <div class="bg-{{ $status['class'] }} bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-{{ $status['icon'] }} fa-2x text-{{ $status['class'] }}"></i>
                    </div>
                    <h5 class="text-{{ $status['class'] }} mb-2">{{ $status['text'] }}</h5>
                    <small class="text-muted">
                        Atualizado em {{ isset($transaction->updated_at) && $transaction->updated_at ? $transaction->updated_at->format('d/m/Y H:i') : 'N/A' }}
                    </small>
                </div>
                
                @if($transactionStatus === 'pending')
                    <div class="d-grid">
                        <button class="btn btn-success" onclick="reconcileTransaction()">
                            <i class="fas fa-check-circle me-2"></i>Conciliar Agora
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Informações Adicionais -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-info text-info me-2"></i>Informações Adicionais
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted d-block">Data de Criação</small>
                        <strong>{{ isset($transaction->created_at) && $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : 'N/A' }}</strong>
                    </div>
                    
                    <div class="col-12">
                        <small class="text-muted d-block">Última Atualização</small>
                        <strong>{{ isset($transaction->updated_at) && $transaction->updated_at ? $transaction->updated_at->format('d/m/Y H:i') : 'N/A' }}</strong>
                    </div>
                    
                    @if(isset($transaction->reconciliation_id) && $transaction->reconciliation_id)
                    <div class="col-12">
                        <small class="text-muted d-block">ID da Conciliação</small>
                        <strong>{{ $transaction->reconciliation_id }}</strong>
                    </div>
                    @endif
                    
                    @if(isset($transaction->import_hash) && $transaction->import_hash)
                    <div class="col-12">
                        <small class="text-muted d-block">Hash de Importação</small>
                        <code class="small">{{ Str::limit($transaction->import_hash, 10) }}</code>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($transactionStatus !== 'reconciled')
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Editar Transação
                        </a>
                    @endif
                    
                    <button class="btn btn-outline-info" onclick="duplicateTransaction()">
                        <i class="fas fa-copy me-2"></i>Duplicar
                    </button>
                    
                    <button class="btn btn-outline-secondary" onclick="exportTransaction()">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                    
                    <a href="{{ route('transactions.index', ['account' => $transaction->bank_account_id ?? '']) }}" class="btn btn-outline-dark">
                        <i class="fas fa-list me-2"></i>Ver da Mesma Conta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reconcileTransaction() {
    if (confirm('Deseja conciliar esta transação?')) {
        // Implementar funcionalidade de conciliação
        alert('Funcionalidade de conciliação será implementada em breve.');
    }
}

function duplicateTransaction() {
    if (confirm('Deseja criar uma nova transação com os mesmos dados?')) {
        // Implementar funcionalidade de duplicação
        window.location.href = "{{ route('transactions.create', ['duplicate' => $transaction->id ?? '']) }}";
    }
}

function exportTransaction() {
    // Implementar funcionalidade de exportação
    alert('Funcionalidade de exportação será implementada em breve.');
}

function deleteTransaction() {
    if (confirm('Tem certeza que deseja excluir esta transação? Esta ação não pode ser desfeita.')) {
        // Implementar funcionalidade de exclusão
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('transactions.destroy', $transaction) }}";
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

<style>
.timeline-item {
    position: relative;
    padding: 10px 0 20px 30px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -25px;
    top: 20px;
    bottom: -10px;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 15px;
}
</style>
@endpush
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para alternar status de conciliação
    window.toggleReconciliation = function() {
        const currentStatus = {{ $transaction->is_reconciled ? 'true' : 'false' }};
        const newStatus = !currentStatus;
        
        // Em produção, fazer requisição AJAX para atualizar
        showToast(`Transação marcada como ${newStatus ? 'conciliada' : 'pendente'}!`, 'success');
        
        // Recarregar página após 1 segundo
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    };
    
    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl+E para editar
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            window.location.href = "{{ route('transactions.edit', $transaction) }}";
        }
        
        // Ctrl+D para excluir
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        
        // R para alternar conciliação
        if (e.key === 'r' || e.key === 'R') {
            e.preventDefault();
            toggleReconciliation();
        }
    });
    
    // Toast de feedback
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
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
                                <th>Categoria:</th>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $transaction->category ?? 'Sem categoria' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Referência:</th>
                                <td>{{ $transaction->reference_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Tipo:</th>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                        {{ $transaction->type == 'credit' ? 'Crédito' : 'Débito' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Valor:</th>
                                <td class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }} fs-5">
                                    <strong>{{ $transaction->formatted_amount }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ 
                                        $transaction->status == 'reconciled' ? 'success' : 
                                        ($transaction->status == 'pending' ? 'warning' : 'danger') 
                                    }}">
                                        {{ $transaction->status == 'reconciled' ? 'Conciliado' : 
                                           ($transaction->status == 'pending' ? 'Pendente' : 'Erro') }}
                                    </span>
                                </td>
                            </tr>
                            @if($transaction->reconciliation)
                            <tr>
                                <th>Conciliação:</th>
                                <td>
                                    <a href="{{ route('reconciliations.show', $transaction->reconciliation) }}">
                                        #{{ $transaction->reconciliation->id }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Criado em:</th>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a>
                    
                    @if($transaction->status == 'pending')
                    <form action="{{ route('transactions.reconcile') }}" method="POST">
                        @csrf
                        <input type="hidden" name="transaction_ids[]" value="{{ $transaction->id }}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i> Marcar como Conciliada
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('transactions.index', ['bank_account_id' => $transaction->bank_account_id]) }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-list me-2"></i> Ver Todas da Conta
                    </a>
                    
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar para Lista
                    </a>
                    
                    @if($transaction->status != 'reconciled')
                    <hr>
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" 
                          onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i> Excluir
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
