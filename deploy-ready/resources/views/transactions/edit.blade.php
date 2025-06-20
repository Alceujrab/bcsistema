@extends('layouts.app')

@section('title', 'Editar Transação')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-edit text-warning me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Editar Transação</h2>
        <p class="text-muted mb-0">Modificar informações da transação</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-outline-secondary">
        <i class="fas fa-eye me-2"></i>Visualizar
    </a>
    <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('transactions.update', $transaction) }}" method="POST" id="transactionForm">
    @csrf
    @method('PATCH')
    
    <div class="row">
        <!-- Formulário Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-form text-warning me-2"></i>Informações da Transação
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Alerta de Status -->
                    @if(isset($transaction->status) && $transaction->status === 'reconciled')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta transação já foi conciliada. Editar pode afetar a conciliação.
                        </div>
                    @endif
                    
                    <!-- Erros de Validação -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-circle me-2"></i>Corrija os erros abaixo:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="row">
                        <!-- Data da Transação -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">
                                <i class="fas fa-calendar text-primary me-2"></i>Data da Transação *
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('transaction_date') is-invalid @enderror" 
                                   id="transaction_date" 
                                   name="transaction_date" 
                                   value="{{ old('transaction_date', isset($transaction->transaction_date) ? $transaction->transaction_date->format('Y-m-d\TH:i') : '') }}" 
                                   required>
                            @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tipo de Transação -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                <i class="fas fa-exchange-alt text-info me-2"></i>Tipo de Transação *
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Selecione o tipo</option>
                                <option value="credit" {{ old('type', $transaction->type ?? '') === 'credit' ? 'selected' : '' }}>
                                    <i class="fas fa-plus-circle"></i> Crédito (Entrada)
                                </option>
                                <option value="debit" {{ old('type', $transaction->type ?? '') === 'debit' ? 'selected' : '' }}>
                                    <i class="fas fa-minus-circle"></i> Débito (Saída)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Conta Bancária -->
                        <div class="col-md-6 mb-3">
                            <label for="bank_account_id" class="form-label">
                                <i class="fas fa-university text-success me-2"></i>Conta Bancária *
                            </label>
                            <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id" required>
                                <option value="">Selecione a conta</option>
                                @if(isset($bankAccounts))
                                    @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}" 
                                                {{ old('bank_account_id', $transaction->bank_account_id ?? '') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} - {{ $account->bank_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('bank_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Valor -->
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-dollar-sign text-success me-2"></i>Valor *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount', $transaction->amount ?? '') }}" 
                                       step="0.01" 
                                       min="0.01" 
                                       placeholder="0,00" 
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-file-text text-secondary me-2"></i>Descrição *
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Descreva a transação..." 
                                  required>{{ old('description', $transaction->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <!-- Categoria -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                <i class="fas fa-tag text-warning me-2"></i>Categoria
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Selecione uma categoria</option>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $transaction->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Número de Referência -->
                        <div class="col-md-6 mb-3">
                            <label for="reference_number" class="form-label">
                                <i class="fas fa-hashtag text-info me-2"></i>Número de Referência
                            </label>
                            <input type="text" 
                                   class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" 
                                   name="reference_number" 
                                   value="{{ old('reference_number', $transaction->reference_number ?? '') }}" 
                                   placeholder="Ex: DOC123456">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">
                            <i class="fas fa-flag text-secondary me-2"></i>Status da Transação
                        </label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="pending" {{ old('status', $transaction->status ?? 'pending') === 'pending' ? 'selected' : '' }}>
                                <i class="fas fa-clock"></i> Pendente
                            </option>
                            <option value="reconciled" {{ old('status', $transaction->status ?? '') === 'reconciled' ? 'selected' : '' }}>
                                <i class="fas fa-check-circle"></i> Conciliada
                            </option>
                            <option value="cancelled" {{ old('status', $transaction->status ?? '') === 'cancelled' ? 'selected' : '' }}>
                                <i class="fas fa-times-circle"></i> Cancelada
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Resetar
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>Visualizar
                            </a>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Painel Lateral -->
        <div class="col-lg-4">
            <!-- Preview da Transação -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-eye text-info me-2"></i>Preview da Transação
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded p-3 mb-3">
                            <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted mb-0">Visualização</h6>
                        </div>
                        
                        <div id="preview-amount" class="fs-4 fw-bold text-success mb-2">
                            R$ {{ number_format($transaction->amount ?? 0, 2, ',', '.') }}
                        </div>
                        
                        <div id="preview-type" class="badge bg-secondary mb-2">
                            {{ $transaction->type === 'credit' ? 'Crédito' : 'Débito' }}
                        </div>
                        
                        <p id="preview-description" class="text-muted small">
                            {{ $transaction->description ?? 'Sem descrição' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Informações Adicionais -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-secondary me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ID:</span>
                            <strong>{{ $transaction->id ?? 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Criada:</span>
                            <strong>{{ isset($transaction->created_at) ? $transaction->created_at->format('d/m/Y') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Atualizada:</span>
                            <strong>{{ isset($transaction->updated_at) ? $transaction->updated_at->format('d/m/Y') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status Atual:</span>
                            <span class="badge bg-{{ ($transaction->status ?? 'pending') === 'reconciled' ? 'success' : 'warning' }}">
                                {{ ($transaction->status ?? 'pending') === 'reconciled' ? 'Conciliada' : 'Pendente' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ajuda -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle text-info me-2"></i>Ajuda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <p><strong>Dicas para edição:</strong></p>
                        <ul class="mb-0">
                            <li>Campos com * são obrigatórios</li>
                            <li>O valor deve ser positivo</li>
                            <li>Data não pode ser futura</li>
                            <li>Transações conciliadas requerem atenção especial</li>
                            <li>Alterações podem afetar relatórios</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview em tempo real
    const amountInput = document.getElementById('amount');
    const typeSelect = document.getElementById('type');
    const descriptionInput = document.getElementById('description');
    
    const previewAmount = document.getElementById('preview-amount');
    const previewType = document.getElementById('preview-type');
    const previewDescription = document.getElementById('preview-description');
    
    function updatePreview() {
        // Atualizar valor
        const amount = parseFloat(amountInput.value) || 0;
        const formattedAmount = 'R$ ' + amount.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        previewAmount.textContent = formattedAmount;
        
        // Atualizar tipo e cor
        const type = typeSelect.value;
        if (type === 'credit') {
            previewType.textContent = 'Crédito';
            previewType.className = 'badge bg-success mb-2';
            previewAmount.className = 'fs-4 fw-bold text-success mb-2';
        } else if (type === 'debit') {
            previewType.textContent = 'Débito';
            previewType.className = 'badge bg-danger mb-2';
            previewAmount.className = 'fs-4 fw-bold text-danger mb-2';
        } else {
            previewType.textContent = 'Tipo não selecionado';
            previewType.className = 'badge bg-secondary mb-2';
            previewAmount.className = 'fs-4 fw-bold text-muted mb-2';
        }
        
        // Atualizar descrição
        const description = descriptionInput.value || 'Sem descrição';
        previewDescription.textContent = description;
    }
    
    // Event listeners
    amountInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    
    // Atualizar preview inicial
    updatePreview();
});

function resetForm() {
    if (confirm('Tem certeza que deseja resetar o formulário? Todas as alterações não salvas serão perdidas.')) {
        document.getElementById('transactionForm').reset();
        setTimeout(() => {
            updatePreview();
        }, 100);
    }
}

// Validação customizada
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amount').value);
    const date = new Date(document.getElementById('transaction_date').value);
    const today = new Date();
    
    if (amount <= 0) {
        e.preventDefault();
        alert('O valor deve ser maior que zero.');
        return false;
    }
    
    if (date > today) {
        e.preventDefault();
        alert('A data da transação não pode ser futura.');
        return false;
    }
});
</script>
@endpush
@endsection
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>Editar Dados da Transação
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST" id="editTransactionForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informações da Conta (Readonly) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-university text-primary me-2"></i>Conta Bancária
                            </label>
                            <input type="text" class="form-control form-control-lg" 
                                   value="{{ $transaction->bankAccount->name }}" disabled>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                A conta não pode ser alterada após a criação
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar text-info me-2"></i>Data da Transação
                            </label>
                            <input type="date" name="transaction_date" 
                                   class="form-control form-control-lg @error('transaction_date') is-invalid @enderror" 
                                   value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" 
                                   required>
                            @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Data em que a transação foi realizada
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tipo e Valor -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-exchange-alt text-success me-2"></i>Tipo de Transação
                            </label>
                            <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                <option value="">Selecione o tipo</option>
                                <option value="credit" {{ old('type', $transaction->type) == 'credit' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-up me-2"></i>Crédito (+)
                                </option>
                                <option value="debit" {{ old('type', $transaction->type) == 'debit' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-down me-2"></i>Débito (-)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Define se é entrada ou saída de dinheiro
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Valor
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $transaction->amount) }}" 
                                       step="0.01" 
                                       placeholder="0,00" 
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Valor da transação sem símbolos
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descrição e Categoria -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-alt text-secondary me-2"></i>Descrição
                            </label>
                            <input type="text" name="description" 
                                   class="form-control form-control-lg @error('description') is-invalid @enderror" 
                                   value="{{ old('description', $transaction->description) }}" 
                                   placeholder="Descrição da transação..." 
                                   required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Descreva brevemente a transação
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag text-warning me-2"></i>Categoria
                            </label>
                            <select name="category_id" class="form-select form-select-lg @error('category_id') is-invalid @enderror">
                                <option value="">Sem categoria</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Opcional, ajuda na organização
                            </div>
                        </div>
                    </div>
                    
                    <!-- Referência -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-hashtag text-info me-2"></i>Referência/Documento
                        </label>
                        <input type="text" name="reference" 
                               class="form-control form-control-lg @error('reference') is-invalid @enderror" 
                               value="{{ old('reference', $transaction->reference) }}" 
                               placeholder="Número do documento, cheque, transferência, etc.">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Campo opcional para referência da transação
                        </div>
                    </div>
                    
                    <!-- Status da Conciliação -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-balance-scale text-primary me-2"></i>Status da Conciliação
                            </label>
                            <select name="is_reconciled" class="form-select form-select-lg">
                                <option value="0" {{ old('is_reconciled', $transaction->is_reconciled) == '0' ? 'selected' : '' }}>
                                    <i class="fas fa-clock me-2"></i>Pendente
                                </option>
                                <option value="1" {{ old('is_reconciled', $transaction->is_reconciled) == '1' ? 'selected' : '' }}>
                                    <i class="fas fa-check me-2"></i>Conciliada
                                </option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Status da conciliação bancária
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral com Informações -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informações da Transação
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted">ID:</th>
                        <td class="fw-bold">#{{ $transaction->id }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Criada em:</th>
                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Última Atualização:</th>
                        <td>{{ $transaction->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status Atual:</th>
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
                </table>
            </div>
        </div>
        
        <!-- Dicas de Edição -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Dicas de Edição
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Conta Bancária:</strong> Não pode ser alterada após a criação da transação.
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Cuidado:</strong> Alterar o valor ou tipo pode afetar o saldo da conta.
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-check me-2"></i>
                    <strong>Conciliação:</strong> Marque como conciliada apenas após confirmar no extrato bancário.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle text-warning me-2"></i>Confirmar Alterações
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja salvar as alterações na transação?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    As alterações podem afetar o saldo da conta bancária e relatórios.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmSave">
                    <i class="fas fa-save me-2"></i>Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
                            <label class="form-label">Tipo</label>
                            <input type="text" class="form-control" value="{{ $transaction->type == 'credit' ? 'Crédito' : 'Débito' }}" 
disabled>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Valor</label>
                            <input type="text" class="form-control" value="R$ {{ number_format($transaction->amount, 2, ',', '.') }}" 
disabled>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                               value="{{ old('description', $transaction->description) }}" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Categoria</label>
                            <select name="category" class="form-select">
                                <option value="">Sem categoria</option>
                                <option value="Alimentação" {{ old('category', $transaction->category) == 'Alimentação' ? 'selected' : 
'' }}>Alimentação</option>
                                <option value="Transporte" {{ old('category', $transaction->category) == 'Transporte' ? 'selected' : '' 
}}>Transporte</option>
                                <option value="Saúde" {{ old('category', $transaction->category) == 'Saúde' ? 'selected' : '' 
}}>Saúde</option>
                                <option value="Educação" {{ old('category', $transaction->category) == 'Educação' ? 'selected' : '' 
}}>Educação</option>
                                <option value="Lazer" {{ old('category', $transaction->category) == 'Lazer' ? 'selected' : '' 
}}>Lazer</option>
                                <option value="Moradia" {{ old('category', $transaction->category) == 'Moradia' ? 'selected' : '' 
}}>Moradia</option>
                                <option value="Tecnologia" {{ old('category', $transaction->category) == 'Tecnologia' ? 'selected' : '' 
}}>Tecnologia</option>
                                <option value="Transferência" {{ old('category', $transaction->category) == 'Transferência' ? 'selected' 
: '' }}>Transferência</option>
                                <option value="Vendas" {{ old('category', $transaction->category) == 'Vendas' ? 'selected' : '' 
}}>Vendas</option>
                                <option value="Outros" {{ old('category', $transaction->category) == 'Outros' ? 'selected' : '' 
}}>Outros</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' 
}}>Pendente</option>
                                <option value="reconciled" {{ old('status', $transaction->status) == 'reconciled' ? 'selected' : '' 
}}>Conciliado</option>
                                <option value="error" {{ old('status', $transaction->status) == 'error' ? 'selected' : '' 
}}>Erro</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações</h5>
            </div>
            <div class="card-body">
                <p><strong>Referência:</strong> {{ $transaction->reference_number ?? '-' }}</p>
                <p><strong>Criado em:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Atualizado em:</strong> {{ $transaction->updated_at->format('d/m/Y H:i') }}</p>
                
                @if($transaction->reconciliation)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta transação está vinculada à conciliação 
                    <a href="{{ route('reconciliations.show', $transaction->reconciliation) }}">
                        #{{ $transaction->reconciliation->id }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
