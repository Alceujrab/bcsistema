@extends('layouts.app')

@section('title', 'Nova Conciliação Bancária')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-plus-circle text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Nova Conciliação Bancária</h2>
        <p class="text-muted mb-0">Crie uma nova conciliação para reconciliar suas transações</p>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Formulário Principal -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Dados da Conciliação
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reconciliations.store') }}" method="POST" id="reconciliationForm">
                    @csrf
                    
                    <!-- Seleção da Conta Bancária -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-university text-primary me-2"></i>Conta Bancária
                        </label>
                        <select name="bank_account_id" id="bank_account_id" 
                                class="form-select form-select-lg @error('bank_account_id') is-invalid @enderror" 
                                required>
                            <option value="">Selecione uma conta bancária</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" 
                                    {{ old('bank_account_id', request('bank_account_id')) == $account->id ? 'selected' : '' }}
                                    data-bank="{{ $account->bank_name }}"
                                    data-account="{{ $account->account_number }}">
                                    {{ $account->name }} - {{ $account->bank_name }}
                                    @if($account->account_number)
                                        ({{ $account->account_number }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Selecione a conta que será conciliada
                        </div>
                    </div>
                    
                    <!-- Período da Conciliação -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-success me-2"></i>Data Inicial
                            </label>
                            <input type="date" name="start_date" 
                                   class="form-control form-control-lg @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', $lastReconciliation ? $lastReconciliation->end_date->addDay()->format('Y-m-d') : '') }}" 
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Data de início do período a conciliar
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-check text-info me-2"></i>Data Final
                            </label>
                            <input type="date" name="end_date" 
                                   class="form-control form-control-lg @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', date('Y-m-d')) }}" 
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Data final do período a conciliar
                            </div>
                        </div>
                    </div>
                    
                    <!-- Saldos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-arrow-up text-success me-2"></i>Saldo Inicial
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" name="starting_balance" 
                                       class="form-control @error('starting_balance') is-invalid @enderror" 
                                       value="{{ old('starting_balance', $lastReconciliation ? $lastReconciliation->ending_balance : '0.00') }}" 
                                       step="0.01" 
                                       placeholder="0,00"
                                       required>
                                @error('starting_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Saldo no início do período (conforme sistema)
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-arrow-down text-info me-2"></i>Saldo Final (Extrato)
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-info text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" name="ending_balance" 
                                       class="form-control @error('ending_balance') is-invalid @enderror" 
                                       value="{{ old('ending_balance') }}" 
                                       step="0.01" 
                                       placeholder="0,00"
                                       required>
                                @error('ending_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Saldo conforme extrato bancário
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-sticky-note text-warning me-2"></i>Observações
                        </label>
                        <textarea name="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Digite observações importantes sobre esta conciliação...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Informações adicionais sobre a conciliação (opcional)
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('reconciliations.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="draft" class="btn btn-outline-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar como Rascunho
                            </button>
                            <button type="submit" name="action" value="create" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>Criar Conciliação
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Última Conciliação -->
        @if($lastReconciliation)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>Última Conciliação
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded p-2 me-3">
                        <i class="fas fa-university text-success"></i>
                    </div>
                    <div>
                        <strong>{{ $lastReconciliation->bankAccount->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $lastReconciliation->bankAccount->bank_name }}</small>
                    </div>
                </div>
                
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="text-muted small">Período</div>
                            <div class="fw-bold small">
                                {{ $lastReconciliation->start_date->format('d/m/Y') }}
                                <br>
                                {{ $lastReconciliation->end_date->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Saldo Final</div>
                        <div class="fw-bold text-success">
                            R$ {{ number_format($lastReconciliation->ending_balance, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check-circle me-1"></i>
                        {{ ucfirst($lastReconciliation->status) }}
                    </span>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Guia de Instruções -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Como Funciona
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">
                            <i class="fas fa-university text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">1. Selecione a Conta</h6>
                            <p class="text-muted small mb-0">Escolha a conta bancária que será conciliada</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">
                            <i class="fas fa-calendar text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">2. Defina o Período</h6>
                            <p class="text-muted small mb-0">Informe as datas de início e fim da conciliação</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">3. Informe os Saldos</h6>
                            <p class="text-muted small mb-0">Digite o saldo inicial e final conforme extrato</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">4. Processamento</h6>
                            <p class="text-muted small mb-0">O sistema identificará automaticamente as transações</p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-light border-primary mt-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-magic text-primary me-2"></i>
                        <div class="small">
                            <strong>Dica:</strong> As transações do período serão associadas automaticamente à conciliação.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    padding-left: 15px;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.input-group-text {
    border: 1px solid #ced4da;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Atualizar URL quando conta bancária for selecionada
    $('#bank_account_id').on('change', function() {
        const accountId = $(this).val();
        if (accountId) {
            const url = new URL(window.location);
            url.searchParams.set('bank_account_id', accountId);
            window.history.pushState({}, '', url);
        }
    });
    
    // Validação do formulário
    $('#reconciliationForm').on('submit', function(e) {
        const startDate = new Date($('input[name="start_date"]').val());
        const endDate = new Date($('input[name="end_date"]').val());
        
        if (startDate >= endDate) {
            e.preventDefault();
            alert('A data final deve ser posterior à data inicial.');
            return false;
        }
        
        const startingBalance = parseFloat($('input[name="starting_balance"]').val());
        const endingBalance = parseFloat($('input[name="ending_balance"]').val());
        
        if (isNaN(startingBalance) || isNaN(endingBalance)) {
            e.preventDefault();
            alert('Por favor, informe valores válidos para os saldos.');
            return false;
        }
    });
    
    // Formatação de valores monetários
    $('input[name="starting_balance"], input[name="ending_balance"]').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
    
    // Calcular diferença em tempo real
    function calculateDifference() {
        const startingBalance = parseFloat($('input[name="starting_balance"]').val()) || 0;
        const endingBalance = parseFloat($('input[name="ending_balance"]').val()) || 0;
        const difference = endingBalance - startingBalance;
        
        // Você pode adicionar um elemento para mostrar a diferença em tempo real
        console.log('Diferença calculada:', difference);
    }
    
    $('input[name="starting_balance"], input[name="ending_balance"]').on('input', calculateDifference);
});
</script>
@endpush
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if($lastReconciliation)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Última Conciliação</h5>
            </div>
            <div class="card-body">
                <p><strong>Período:</strong> {{ $lastReconciliation->start_date->format('d/m/Y') }} a {{ 
$lastReconciliation->end_date->format('d/m/Y') }}</p>
                <p><strong>Saldo Final:</strong> R$ {{ number_format($lastReconciliation->ending_balance, 2, ',', '.') }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-success">{{ ucfirst($lastReconciliation->status) }}</span>
                </p>
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Instruções</h5>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>Selecione a conta bancária</li>
                    <li>Defina o período da conciliação</li>
                    <li>Informe o saldo inicial (geralmente o saldo final da última conciliação)</li>
                    <li>Informe o saldo final conforme o extrato bancário</li>
                    <li>O sistema identificará automaticamente as transações do período</li>
                </ol>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    As transações pendentes do período serão associadas automaticamente a esta conciliação.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#bank_account_id').on('change', function() {
        if ($(this).val()) {
            window.location.href = '{{ route("reconciliations.create") }}?bank_account_id=' + $(this).val();
        }
    });
});
</script>
@endpush
