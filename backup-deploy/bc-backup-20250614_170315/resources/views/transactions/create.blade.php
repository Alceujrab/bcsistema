@extends('layouts.app')

@section('title', 'Nova Transação')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-primary me-2"></i>
            Nova Transação
        </h1>
        <p class="text-muted mb-0">Cadastrar uma nova transação bancária</p>
    </div>
    <div>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dados da Transação</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bank_account_id" class="form-label">Conta Bancária *</label>
                            <select name="bank_account_id" id="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                                <option value="">Selecione uma conta...</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label">Data da Transação *</label>
                            <input type="date" name="transaction_date" id="transaction_date" 
                                   class="form-control @error('transaction_date') is-invalid @enderror" 
                                   value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição *</label>
                        <input type="text" name="description" id="description" 
                               class="form-control @error('description') is-invalid @enderror" 
                               value="{{ old('description') }}" placeholder="Descrição da transação" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label">Tipo *</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Selecione o tipo...</option>
                                <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>Crédito (Entrada)</option>
                                <option value="debit" {{ old('type') == 'debit' ? 'selected' : '' }}>Débito (Saída)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Valor *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" step="0.01" min="0" placeholder="0,00" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Categoria</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Nenhuma categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="reference_number" class="form-label">Número de Referência</label>
                            <input type="text" name="reference_number" id="reference_number" 
                                   class="form-control @error('reference_number') is-invalid @enderror" 
                                   value="{{ old('reference_number') }}" placeholder="Ex: 123456">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Transação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format amount input
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d.]/g, '');
        this.value = value;
    });
});
</script>
@endsection
