@extends('layouts.app')

@section('title', 'Editar ' . $bankAccount->name)

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-edit text-warning me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Editar Conta Bancária</h2>
        <p class="text-muted mb-0">{{ $bankAccount->name }} - {{ $bankAccount->bank_name }}</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <a href="{{ route('bank-accounts.show', $bankAccount) }}" class="btn btn-outline-secondary">
        <i class="fas fa-eye me-2"></i>Visualizar
    </a>
    <a href="{{ route('bank-accounts.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-university me-2"></i>Editar Dados da Conta
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bank-accounts.update', $bankAccount) }}" method="POST" id="editAccountForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">
                                <i class="fas fa-signature text-primary me-2"></i>Nome da Conta
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $bankAccount->name) }}" 
                                   placeholder="Ex: Conta Corrente Principal"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Use um nome descritivo para identificar facilmente
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label fw-bold">
                                <i class="fas fa-building text-success me-2"></i>Nome do Banco
                            </label>
                            <input type="text" name="bank_name" id="bank_name" 
                                   class="form-control form-control-lg @error('bank_name') is-invalid @enderror" 
                                   value="{{ old('bank_name', $bankAccount->bank_name) }}" 
                                   placeholder="Ex: Banco do Brasil"
                                   required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Nome completo da instituição bancária
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dados Bancários -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="account_number" class="form-label fw-bold">
                                <i class="fas fa-hashtag text-info me-2"></i>Número da Conta
                            </label>
                            <input type="text" name="account_number" id="account_number" 
                                   class="form-control form-control-lg @error('account_number') is-invalid @enderror" 
                                   value="{{ old('account_number', $bankAccount->account_number) }}" 
                                   placeholder="Ex: 12345-6"
                                   required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Inclua o dígito verificador
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="agency" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-warning me-2"></i>Agência
                            </label>
                            <input type="text" name="agency" id="agency" 
                                   class="form-control form-control-lg @error('agency') is-invalid @enderror" 
                                   value="{{ old('agency', $bankAccount->agency) }}" 
                                   placeholder="Ex: 1234">
                            @error('agency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Opcional para alguns tipos de conta
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="type" class="form-label fw-bold">
                                <i class="fas fa-tags text-danger me-2"></i>Tipo de Conta
                            </label>
                            <select name="type" id="type" 
                                    class="form-select form-select-lg @error('type') is-invalid @enderror" 
                                    required>
                                <option value="">Selecione o tipo</option>
                                <option value="checking" {{ old('type', $bankAccount->type) == 'checking' ? 'selected' : '' }}>
                                    💳 Conta Corrente
                                </option>
                                <option value="savings" {{ old('type', $bankAccount->type) == 'savings' ? 'selected' : '' }}>
                                    🐷 Poupança
                                </option>
                                <option value="investment" {{ old('type', $bankAccount->type) == 'investment' ? 'selected' : '' }}>
                                    📈 Investimento
                                </option>
                                <option value="credit_card" {{ old('type', $bankAccount->type) == 'credit_card' ? 'selected' : '' }}>
                                    💎 Cartão de Crédito
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Define como as transações serão tratadas
                            </div>
                        </div>
                    </div>
                    
                    <!-- Código do Banco -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="bank_code" class="form-label fw-bold">
                                <i class="fas fa-code text-info me-2"></i>Código do Banco
                            </label>
                            <input type="text" name="bank_code" id="bank_code" 
                                   class="form-control form-control-lg @error('bank_code') is-invalid @enderror" 
                                   value="{{ old('bank_code', $bankAccount->bank_code) }}" 
                                   placeholder="Ex: 001, 237, 341"
                                   maxlength="10">
                            @error('bank_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Código do banco para identificação (opcional)
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4 pt-3">
                                <input class="form-check-input" type="checkbox" id="active" 
                                       name="active" value="1" 
                                       {{ old('active', $bankAccount->active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">
                                    <i class="fas fa-toggle-on text-success me-2"></i>Conta Ativa
                                </label>
                                <div class="form-text">
                                    Desmarque para desativar a conta
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Saldo Inicial -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="balance" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Saldo Atual
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="balance" id="balance" 
                                       class="form-control @error('balance') is-invalid @enderror" 
                                       value="{{ old('balance', $bankAccount->balance) }}" 
                                       step="0.01" 
                                       placeholder="0,00">
                                @error('balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Saldo atual da conta
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-info me-2"></i>Status
                            </label>
                            <select name="status" id="status" 
                                    class="form-select form-select-lg @error('status') is-invalid @enderror" 
                                    required>
                                <option value="active" {{ old('status', $bankAccount->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle text-success me-2"></i>Ativa
                                </option>
                                <option value="inactive" {{ old('status', $bankAccount->status ?? 'active') == 'inactive' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle text-danger me-2"></i>Inativa
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Contas inativas não aparecerão em relatórios
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-sticky-note text-secondary me-2"></i>Observações
                        </label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Informações adicionais sobre a conta...">{{ old('description', $bankAccount->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Campo opcional para informações adicionais
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('bank-accounts.index') }}" class="btn btn-outline-secondary btn-lg">
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
    
    <!-- Painel Lateral com Estatísticas -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Estatísticas da Conta
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-wallet text-primary fa-2x mb-2"></i>
                            <h5 class="mb-0 {{ $bankAccount->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($bankAccount->balance ?? 0, 2, ',', '.') }}
                            </h5>
                            <small class="text-muted">Saldo Atual</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-list text-secondary fa-2x mb-2"></i>
                            <h5 class="mb-0">{{ $bankAccount->transactions_count ?? 0 }}</h5>
                            <small class="text-muted">Transações</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dicas de Preenchimento -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Dicas de Preenchimento
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Nome da Conta:</strong> Use nomes descritivos como "Conta Corrente Principal" ou "Poupança Reserva".
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Tipo de Conta:</strong> O tipo afeta como os saldos são calculados nos relatórios.
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-check me-2"></i>
                    <strong>Saldo Inicial:</strong> Use o saldo real da conta no momento da criação no sistema.
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
                <p>Tem certeza que deseja salvar as alterações na conta bancária?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    As alterações afetarão todos os relatórios e cálculos relacionados a esta conta.
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editAccountForm');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    // Interceptar submit do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        confirmModal.show();
    });
    
    // Confirmar salvamento
    document.getElementById('confirmSave').addEventListener('click', function() {
        confirmModal.hide();
        form.submit();
    });
    
    // Validação em tempo real
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', validateInput);
        input.addEventListener('input', clearErrors);
    });
    
    function validateInput(e) {
        const input = e.target;
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    }
    
    function clearErrors(e) {
        const input = e.target;
        input.classList.remove('is-invalid');
    }
    
    // Formatação do saldo
    const balanceInput = document.getElementById('balance');
    if (balanceInput) {
        balanceInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d.-]/g, '');
            if (value) {
                e.target.value = parseFloat(value).toFixed(2);
            }
        });
    }
    
    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl+S para salvar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
        
        // Esc para cancelar
        if (e.key === 'Escape' && !document.querySelector('.modal.show')) {
            window.location.href = "{{ route('bank-accounts.index') }}";
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
