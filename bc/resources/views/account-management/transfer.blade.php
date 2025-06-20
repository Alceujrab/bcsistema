@extends('layouts.app')

@section('title', 'Transferência entre Contas')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('account-management.index') }}">Gestão de Contas</a></li>
        <li class="breadcrumb-item active">Transferência</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Transferência entre Contas
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('account-management.transfer.process') }}" id="transferForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_account_id" class="form-label">
                                        <i class="fas fa-arrow-up text-danger me-2"></i>
                                        Conta de Origem
                                    </label>
                                    <select name="from_account_id" id="from_account_id" class="form-select" required>
                                        <option value="">Selecione a conta de origem</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    data-balance="{{ $account->balance }}"
                                                    {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} - {{ $account->bank_name }}
                                                (Saldo: R$ {{ number_format($account->balance, 2, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <small id="from-account-balance" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="to_account_id" class="form-label">
                                        <i class="fas fa-arrow-down text-success me-2"></i>
                                        Conta de Destino
                                    </label>
                                    <select name="to_account_id" id="to_account_id" class="form-select" required>
                                        <option value="">Selecione a conta de destino</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} - {{ $account->bank_name }}
                                                (Saldo: R$ {{ number_format($account->balance, 2, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">
                                        <i class="fas fa-dollar-sign me-2"></i>
                                        Valor da Transferência
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" 
                                               name="amount" 
                                               id="amount" 
                                               class="form-control" 
                                               step="0.01" 
                                               min="0.01"
                                               value="{{ old('amount') }}" 
                                               required>
                                    </div>
                                    <div class="form-text">
                                        <small id="amount-warning" class="text-warning d-none">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Valor superior ao saldo disponível
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transfer_date" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>
                                        Data da Transferência
                                    </label>
                                    <input type="date" 
                                           name="transfer_date" 
                                           id="transfer_date" 
                                           class="form-control" 
                                           value="{{ old('transfer_date', date('Y-m-d')) }}" 
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-comment me-2"></i>
                                Descrição
                            </label>
                            <input type="text" 
                                   name="description" 
                                   id="description" 
                                   class="form-control" 
                                   value="{{ old('description') }}" 
                                   placeholder="Motivo da transferência"
                                   required>
                        </div>

                        <!-- Resumo da Transferência -->
                        <div class="card bg-light mb-3" id="transfer-summary" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2"></i>
                                    Resumo da Transferência
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>De:</strong> 
                                            <span id="summary-from"></span>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Para:</strong> 
                                            <span id="summary-to"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>Valor:</strong> 
                                            <span id="summary-amount" class="text-primary"></span>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Data:</strong> 
                                            <span id="summary-date"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('account-management.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar
                            </a>
                            
                            <div>
                                <a href="{{ route('account-management.transfer.history') }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-history me-2"></i>
                                    Histórico
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Confirmar Transferência
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromAccountSelect = document.getElementById('from_account_id');
    const toAccountSelect = document.getElementById('to_account_id');
    const amountInput = document.getElementById('amount');
    const transferDateInput = document.getElementById('transfer_date');
    const descriptionInput = document.getElementById('description');
    const transferSummary = document.getElementById('transfer-summary');
    const submitBtn = document.getElementById('submitBtn');
    const amountWarning = document.getElementById('amount-warning');
    const balanceInfo = document.getElementById('from-account-balance');

    // Atualizar saldo disponível
    function updateBalance() {
        const selectedOption = fromAccountSelect.options[fromAccountSelect.selectedIndex];
        if (selectedOption.value) {
            const balance = parseFloat(selectedOption.dataset.balance);
            balanceInfo.textContent = `Saldo disponível: R$ ${balance.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
            
            // Verificar se o valor é maior que o saldo
            const amount = parseFloat(amountInput.value);
            if (amount > balance) {
                amountWarning.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                amountWarning.classList.add('d-none');
                submitBtn.disabled = false;
            }
        } else {
            balanceInfo.textContent = '';
            amountWarning.classList.add('d-none');
        }
    }

    // Atualizar resumo
    function updateSummary() {
        const fromAccount = fromAccountSelect.options[fromAccountSelect.selectedIndex];
        const toAccount = toAccountSelect.options[toAccountSelect.selectedIndex];
        const amount = parseFloat(amountInput.value);
        const date = transferDateInput.value;

        if (fromAccount.value && toAccount.value && amount && date) {
            document.getElementById('summary-from').textContent = fromAccount.text.split(' (')[0];
            document.getElementById('summary-to').textContent = toAccount.text.split(' (')[0];
            document.getElementById('summary-amount').textContent = `R$ ${amount.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
            document.getElementById('summary-date').textContent = new Date(date).toLocaleDateString('pt-BR');
            transferSummary.style.display = 'block';
        } else {
            transferSummary.style.display = 'none';
        }
    }

    // Impedir seleção da mesma conta
    function preventSameAccount() {
        const fromValue = fromAccountSelect.value;
        const toValue = toAccountSelect.value;
        
        if (fromValue && toValue && fromValue === toValue) {
            toAccountSelect.setCustomValidity('Selecione uma conta diferente da conta de origem');
        } else {
            toAccountSelect.setCustomValidity('');
        }
    }

    // Event listeners
    fromAccountSelect.addEventListener('change', function() {
        updateBalance();
        updateSummary();
        preventSameAccount();
    });

    toAccountSelect.addEventListener('change', function() {
        updateSummary();
        preventSameAccount();
    });

    amountInput.addEventListener('input', function() {
        updateBalance();
        updateSummary();
    });

    transferDateInput.addEventListener('change', updateSummary);
    descriptionInput.addEventListener('input', updateSummary);

    // Formatação de moeda
    amountInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (value) {
            this.value = value.toFixed(2);
        }
    });

    // Confirmação antes de enviar
    document.getElementById('transferForm').addEventListener('submit', function(e) {
        const fromAccount = fromAccountSelect.options[fromAccountSelect.selectedIndex].text.split(' (')[0];
        const toAccount = toAccountSelect.options[toAccountSelect.selectedIndex].text.split(' (')[0];
        const amount = parseFloat(amountInput.value);
        
        if (!confirm(`Confirma a transferência de R$ ${amount.toLocaleString('pt-BR', {minimumFractionDigits: 2})} de ${fromAccount} para ${toAccount}?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
