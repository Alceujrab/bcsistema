@extends('layouts.app')

@section('title', 'Transações')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exchange-alt text-primary me-2"></i>
            Transações
        </h1>
        <p class="text-muted mb-0">Gerencie todas as transações bancárias</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nova Transação
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Transações
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_transactions'] ?? 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Crédito
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            R$ {{ number_format($stats['total_credit'] ?? 0, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Débito
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            R$ {{ number_format($stats['total_debit'] ?? 0, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Saldo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            R$ {{ number_format($stats['balance'] ?? 0, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Descrição ou referência">
            </div>
            
            <div class="col-md-2">
                <label for="type" class="form-label">Tipo</label>
                <select class="form-select" id="type" name="type">
                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Crédito</option>
                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Débito</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="date_from" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label for="date_to" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Transações -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Transações</h6>
    </div>
    <div class="card-body">
        @if($transactions && $transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Conta</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $transaction->description }}">
                                    {{ $transaction->description }}
                                </div>
                            </td>
                            <td>
                                {{ $transaction->bankAccount ? $transaction->bankAccount->name : 'N/A' }}
                            </td>
                            <td>
                                @if($transaction->type === 'credit')
                                    <span class="badge bg-success">Crédito</span>
                                @else
                                    <span class="badge bg-danger">Débito</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($transaction->status ?? 'pending') {
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'warning'
                                    };
                                    $statusText = match($transaction->status ?? 'pending') {
                                        'completed' => 'Concluído',
                                        'cancelled' => 'Cancelado',
                                        default => 'Pendente'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $transactions->firstItem() }} até {{ $transactions->lastItem() }} 
                        de {{ number_format($transactions->total()) }} transações
                    </div>
                    <div>
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                <p class="text-muted">Não há transações cadastradas ou que correspondam aos filtros aplicados.</p>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Cadastrar Primeira Transação
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form quando filtros mudarem
    const filters = document.querySelectorAll('#type, #status');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection
