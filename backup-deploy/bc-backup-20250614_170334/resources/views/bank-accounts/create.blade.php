@extends('layouts.app')

@section('title', 'Nova Conta Bancária')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-plus-circle text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Nova Conta Bancária</h2>
        <p class="text-muted mb-0">Adicione uma nova conta para gerenciar transações</p>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-university me-2"></i>Dados da Conta Bancária
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bank-accounts.store') }}" method="POST" id="accountForm">
                    @csrf
                    
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">
                                <i class="fas fa-signature text-primary me-2"></i>Nome da Conta
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
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
                                   value="{{ old('bank_name') }}" 
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
                                   value="{{ old('account_number') }}" 
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
                                   value="{{ old('agency') }}" 
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
                                <i class="fas fa-tag text-secondary me-2"></i>Tipo de Conta
                            </label>
                            <select name="type" id="type" 
                                    class="form-select form-select-lg @error('type') is-invalid @enderror" 
                                    required>
                                <option value="">Selecione o tipo...</option>
                                <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>
                                    💳 Conta Corrente
                                </option>
                                <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>
                                    🐷 Poupança
                                </option>
                                <option value="investment" {{ old('type') == 'investment' ? 'selected' : '' }}>
                                    📈 Investimento
                                </option>
                                <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>
                                    💎 Cartão de Crédito
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Informações Financeiras -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="balance" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-success me-2"></i>Saldo Inicial
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" name="balance" id="balance" 
                                       class="form-control @error('balance') is-invalid @enderror" 
                                       value="{{ old('balance', '0.00') }}" 
                                       step="0.01" 
                                       placeholder="0,00">
                                @error('balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Saldo atual da conta para iniciar a conciliação
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="bank_code" class="form-label fw-bold">
                                <i class="fas fa-code text-info me-2"></i>Código do Banco
                            </label>
                            <input type="text" name="bank_code" id="bank_code" 
                                   class="form-control form-control-lg @error('bank_code') is-invalid @enderror" 
                                   value="{{ old('bank_code') }}" 
                                   placeholder="Ex: 001, 033, 104">
                            @error('bank_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Código BACEN do banco (opcional)
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status e Observações -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" 
                                       {{ old('active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">
                                    <i class="fas fa-toggle-on text-success me-2"></i>Conta Ativa
                                </label>
                                <div class="form-text">
                                    Contas inativas não aparecerão nas opções de transação
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-sticky-note text-warning me-2"></i>Observações
                        </label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Informações adicionais sobre a conta...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('bank-accounts.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="save_new" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Salvar e Nova
                            </button>
                            <button type="submit" name="action" value="save" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Conta
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Guia de Instruções -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Guia de Preenchimento
                </h6>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="guideAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide1">
                                <i class="fas fa-signature text-primary me-2"></i>Nome da Conta
                            </button>
                        </h2>
                        <div id="guide1" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                            <div class="accordion-body small">
                                Use nomes descritivos como "Conta Corrente Principal", "Poupança Reserva", etc.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide2">
                                <i class="fas fa-hashtag text-info me-2"></i>Dados Bancários
                            </button>
                        </h2>
                        <div id="guide2" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                            <div class="accordion-body small">
                                Inclua sempre o dígito verificador no número da conta. A agência é opcional para alguns tipos.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide3">
                                <i class="fas fa-dollar-sign text-success me-2"></i>Saldo Inicial
                            </button>
                        </h2>
                        <div id="guide3" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                            <div class="accordion-body small">
                                Informe o saldo atual da conta. Este será o ponto de partida para suas conciliações.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bancos Populares -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-star me-2"></i>Bancos Populares
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillBankData('Banco do Brasil', '001')">
                            <i class="fas fa-building me-2"></i>Banco do Brasil
                        </button>
                    </div>
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillBankData('Caixa Econômica', '104')">
                            <i class="fas fa-building me-2"></i>Caixa Econômica
                        </button>
                    </div>
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillBankData('Santander', '033')">
                            <i class="fas fa-building me-2"></i>Santander
                        </button>
                    </div>
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillBankData('Itaú', '341')">
                            <i class="fas fa-building me-2"></i>Itaú
                        </button>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillBankData('Bradesco', '237')">
                            <i class="fas fa-building me-2"></i>Bradesco
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
    // Máscara para número da conta
    $('#account_number').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 4) {
            value = value.substring(0, value.length - 1) + '-' + value.substring(value.length - 1);
        }
        $(this).val(value);
    });
    
    // Máscara para agência
    $('#agency').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });
    
    // Máscara para código do banco
    $('#bank_code').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value.substring(0, 3));
    });
    
    // Validação do formulário
    $('#accountForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const bankName = $('#bank_name').val().trim();
        const accountNumber = $('#account_number').val().trim();
        
        if (!name || !bankName || !accountNumber) {
            e.preventDefault();
            showToast('Por favor, preencha todos os campos obrigatórios.', 'danger');
            return false;
        }
        
        if (name.length < 3) {
            e.preventDefault();
            showToast('O nome da conta deve ter pelo menos 3 caracteres.', 'warning');
            return false;
        }
    });
    
    // Auto-complete do nome baseado no banco e tipo
    $('#bank_name, #type').on('change', function() {
        const bank = $('#bank_name').val();
        const type = $('#type').val();
        
        if (bank && type && !$('#name').val()) {
            let typeName = '';
            switch(type) {
                case 'checking': typeName = 'Conta Corrente'; break;
                case 'savings': typeName = 'Poupança'; break;
                case 'investment': typeName = 'Investimento'; break;
                case 'credit_card': typeName = 'Cartão de Crédito'; break;
            }
            $('#name').val(`${typeName} ${bank}`);
        }
    });
});

function fillBankData(bankName, bankCode) {
    $('#bank_name').val(bankName);
    $('#bank_code').val(bankCode);
    showToast(`Dados do ${bankName} preenchidos!`, 'success');
}
</script>
@endpush
