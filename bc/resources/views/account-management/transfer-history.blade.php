@extends('layouts.app')

@section('title', 'Histórico de Transferências')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('account-management.index') }}">Gestão de Contas</a></li>
        <li class="breadcrumb-item active">Histórico de Transferências</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-history me-2"></i>
            Histórico de Transferências
        </h2>
        <div>
            <a href="{{ route('account-management.transfer.form') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Transferência
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="account_id" class="form-label">Conta</label>
                    <select name="account_id" id="account_id" class="form-select">
                        <option value="">Todas as contas</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} - {{ $account->bank_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Data Inicial</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Data Final</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('account-management.transfer.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($groupedTransfers->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Conta Origem</th>
                                <th>Conta Destino</th>
                                <th>Valor</th>
                                <th>Descrição</th>
                                <th>Hash</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedTransfers as $transfer)
                                <tr>
                                    <td>
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($transfer['date'])->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-up text-danger me-2"></i>
                                            <div>
                                                <strong>{{ $transfer['from_account']->name }}</strong><br>
                                                <small class="text-muted">{{ $transfer['from_account']->bank_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-down text-success me-2"></i>
                                            <div>
                                                <strong>{{ $transfer['to_account']->name }}</strong><br>
                                                <small class="text-muted">{{ $transfer['to_account']->bank_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            R$ {{ number_format($transfer['amount'], 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate" style="max-width: 200px;" title="{{ $transfer['description'] }}">
                                            {{ Str::limit($transfer['description'], 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="small">{{ Str::limit($transfer['hash'], 20) }}</code>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-outline-info btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#transferModal{{ $loop->index }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal de Detalhes -->
                                <div class="modal fade" id="transferModal{{ $loop->index }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-exchange-alt me-2"></i>
                                                    Detalhes da Transferência
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Informações Gerais</h6>
                                                        <table class="table table-sm">
                                                            <tr>
                                                                <td><strong>Data:</strong></td>
                                                                <td>{{ \Carbon\Carbon::parse($transfer['date'])->format('d/m/Y H:i') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Valor:</strong></td>
                                                                <td><span class="badge bg-primary">R$ {{ number_format($transfer['amount'], 2, ',', '.') }}</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Hash:</strong></td>
                                                                <td><code>{{ $transfer['hash'] }}</code></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Descrição:</strong></td>
                                                                <td>{{ $transfer['description'] }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Transações Relacionadas</h6>
                                                        @foreach($transfer['transactions'] as $transaction)
                                                            <div class="card mb-2">
                                                                <div class="card-body py-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <strong>{{ $transaction->bankAccount->name }}</strong><br>
                                                                            <small class="text-muted">{{ $transaction->bankAccount->bank_name }}</small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <span class="badge bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                                                                {{ $transaction->type == 'credit' ? '+' : '-' }}R$ {{ number_format(abs($transaction->amount), 2, ',', '.') }}
                                                                            </span><br>
                                                                            <small class="text-muted">{{ ucfirst($transaction->type) }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Nenhuma transferência encontrada</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['account_id', 'date_from', 'date_to']))
                        Não foram encontradas transferências com os filtros aplicados.
                    @else
                        Ainda não foram realizadas transferências entre contas.
                    @endif
                </p>
                <a href="{{ route('account-management.transfer.form') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Fazer primeira transferência
                </a>
            </div>
        </div>
    @endif

    <!-- Resumo -->
    @if($groupedTransfers->count() > 0)
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total de Transferências</h6>
                                <h3 class="mb-0">{{ $groupedTransfers->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exchange-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Volume Total</h6>
                                <h3 class="mb-0">R$ {{ number_format($groupedTransfers->sum('amount'), 2, ',', '.') }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Valor Médio</h6>
                                <h3 class="mb-0">R$ {{ number_format($groupedTransfers->avg('amount'), 2, ',', '.') }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário de filtros quando mudar as datas
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.form.date_from.value && this.form.date_to.value) {
                this.form.submit();
            }
        });
    });
});
</script>
@endpush
