@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-hand-holding-usd text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Contas a Receber</h2>
        <p class="text-muted mb-0">Gerencie suas contas a receber e receitas</p>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Estatísticas -->
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Total</h6>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    </div>
                    <i class="fas fa-list fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Pendentes</h6>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Vencidas</h6>
                        <h4 class="mb-0">{{ $stats['overdue'] }}</h4>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Recebidas</h6>
                        <h4 class="mb-0">{{ $stats['received'] }}</h4>
                    </div>
                    <i class="fas fa-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="flex-grow-1">
                    <h6 class="card-title mb-0">Valor Pendente</h6>
                    <p class="mb-0 small">R$ {{ number_format($stats['pending_amount'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="flex-grow-1">
                    <h6 class="card-title mb-0">Valor Vencido</h6>
                    <p class="mb-0 small">R$ {{ number_format($stats['overdue_amount'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Contas a Receber
                </h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('account-receivables.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nova Conta
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Descrição, fatura ou cliente..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Recebido</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cliente</label>
                <select name="client_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Categoria</label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    <option value="sales" {{ request('category') === 'sales' ? 'selected' : '' }}>Vendas</option>
                    <option value="services" {{ request('category') === 'services' ? 'selected' : '' }}>Serviços</option>
                    <option value="rent" {{ request('category') === 'rent' ? 'selected' : '' }}>Aluguel</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Outros</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="{{ route('account-receivables.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Valor Recebido</th>
                        <th>Restante</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accountReceivables as $account)
                    <tr class="{{ $account->status === 'overdue' ? 'table-danger' : '' }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2">
                                    {{ strtoupper(substr($account->client->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $account->client->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $account->description }}</strong>
                                @if($account->invoice_number)
                                    <br><small class="text-muted">Fatura: {{ $account->invoice_number }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                @switch($account->category)
                                    @case('sales') Vendas @break
                                    @case('services') Serviços @break
                                    @case('rent') Aluguel @break
                                    @default Outros
                                @endswitch
                            </span>
                        </td>
                        <td>{{ $account->formatted_amount }}</td>
                        <td>
                            {{ $account->due_date->format('d/m/Y') }}
                            @if($account->days_until_due < 0)
                                <br><small class="text-danger">{{ abs($account->days_until_due) }} dias atrasado</small>
                            @elseif($account->days_until_due <= 7)
                                <br><small class="text-warning">Vence em {{ $account->days_until_due }} dias</small>
                            @endif
                        </td>
                        <td>{!! $account->status_badge !!}</td>
                        <td>{{ $account->formatted_received_amount }}</td>
                        <td>{{ $account->formatted_remaining_amount }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('account-receivables.show', $account) }}" 
                                   class="btn btn-outline-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('account-receivables.edit', $account) }}" 
                                   class="btn btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($account->status !== 'received')
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="markAsReceived({{ $account->id }})" title="Marcar como Recebido">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete({{ $account->id }})" title="Remover">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                                <p>Nenhuma conta a receber encontrada</p>
                                <a href="{{ route('account-receivables.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Cadastrar Primeira Conta
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($accountReceivables->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $accountReceivables->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover esta conta a receber?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Recebimento -->
<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marcar como Recebido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Confirma o recebimento desta conta?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="receiveForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Confirmar Recebimento</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(accountId) {
    const form = document.getElementById('deleteForm');
    form.action = `/account-receivables/${accountId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function markAsReceived(accountId) {
    const form = document.getElementById('receiveForm');
    form.action = `/account-receivables/${accountId}/receive`;
    
    const modal = new bootstrap.Modal(document.getElementById('receiveModal'));
    modal.show();
}
</script>
@endpush
