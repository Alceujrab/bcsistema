@extends('layouts.app')

@section('title', 'Contas Bancárias')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-university text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Contas Bancárias</h2>
        <p class="text-muted mb-0">Gerencie suas contas e acompanhe saldos</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fas fa-filter me-2"></i>Filtros
    </button>
    <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nova Conta
    </a>
</div>
@endsection

@section('content')
<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Total de Contas</h6>
                        <h3 class="mb-0">{{ $accounts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $accounts->total() : $accounts->count() }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-university fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Contas Ativas</h6>
                        <h3 class="mb-0">{{ $accounts->where('active', true)->count() }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Saldo Total</h6>
                        <h3 class="mb-0">R$ {{ number_format($accounts->sum('balance'), 2, ',', '.') }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Transações</h6>
                        <h3 class="mb-0">{{ $accounts->sum('transactions_count') }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-exchange-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela Principal -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark">
                <i class="fas fa-list me-2 text-primary"></i>Lista de Contas Bancárias
            </h5>
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar contas..." id="searchInput">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-signature me-2 text-muted"></i>Nome
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building me-2 text-muted"></i>Banco
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag me-2 text-muted"></i>Tipo
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-hashtag me-2 text-muted"></i>Número
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <i class="fas fa-dollar-sign me-2 text-muted"></i>Saldo
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-exchange-alt me-2 text-muted"></i>Transações
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-info-circle me-2 text-muted"></i>Status
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                    <tr class="border-0">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                                <div>
                                    <a href="{{ route('bank-accounts.show', $account) }}" 
                                       class="fw-bold text-dark text-decoration-none">
                                        {{ $account->name }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        Criada em {{ $account->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <strong>{{ $account->bank_name }}</strong>
                                    @if($account->bank_code)
                                        <br>
                                        <small class="text-muted">Código: {{ $account->bank_code }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $typeConfig = [
                                    'checking' => ['class' => 'primary', 'icon' => 'credit-card', 'text' => 'Corrente'],
                                    'savings' => ['class' => 'success', 'icon' => 'piggy-bank', 'text' => 'Poupança'],
                                    'investment' => ['class' => 'warning', 'icon' => 'chart-line', 'text' => 'Investimento'],
                                ];
                                $config = $typeConfig[$account->type] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $account->type_name];
                            @endphp
                            <span class="badge bg-{{ $config['class'] }} fs-6">
                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                {{ $config['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-primary">{{ $account->account_number ?? 'N/A' }}</code>
                            @if($account->agency)
                                <br>
                                <small class="text-muted">Ag: {{ $account->agency }}</small>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            <span class="fw-bold fs-5 {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($account->balance >= 0)
                                    <i class="fas fa-arrow-up me-1"></i>
                                @else
                                    <i class="fas fa-arrow-down me-1"></i>
                                @endif
                                R$ {{ number_format(abs($account->balance), 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="bg-light rounded p-2 d-inline-block">
                                <span class="fw-bold text-primary">{{ $account->transactions_count }}</span>
                                <br>
                                <small class="text-muted">transações</small>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-{{ $account->active ? 'success' : 'danger' }} fs-6">
                                <i class="fas fa-{{ $account->active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                {{ $account->active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('bank-accounts.edit', $account) }}" 
                                   class="btn btn-warning btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Editar Conta">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                                <a href="{{ route('imports.create', ['bank_account_id' => $account->id]) }}" 
                                   class="btn btn-success btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Importar Extrato">
                                    <i class="fas fa-file-import me-1"></i>Importar
                                </a>
                                <a href="{{ route('reconciliations.create', ['bank_account_id' => $account->id]) }}" 
                                   class="btn btn-info btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Nova Conciliação">
                                    <i class="fas fa-balance-scale me-1"></i>Conciliar
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-5 text-center">
                            <div class="py-4">
                                <i class="fas fa-university fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma conta bancária encontrada</h5>
                                <p class="text-muted mb-3">Comece adicionando sua primeira conta bancária</p>
                                <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nova Conta
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($accounts instanceof \Illuminate\Pagination\LengthAwarePaginator && $accounts->hasPages())
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Mostrando {{ $accounts->firstItem() }} a {{ $accounts->lastItem() }} 
                de {{ $accounts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $accounts->total() : $accounts->count() }} resultados
            </div>
            {{ $accounts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $accounts->links() : '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter me-2"></i>Filtros Avançados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos os status</option>
                            <option value="active">Ativas</option>
                            <option value="inactive">Inativas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Conta</label>
                        <select class="form-select" name="type">
                            <option value="">Todos os tipos</option>
                            <option value="checking">Conta Corrente</option>
                            <option value="savings">Poupança</option>
                            <option value="investment">Investimento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banco</label>
                        <input type="text" class="form-control" name="bank_name" placeholder="Nome do banco">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Busca em tempo real
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

function applyFilters() {
    // Implementar lógica de filtros
    console.log('Aplicando filtros...');
    $('#filterModal').modal('hide');
}
</script>
@endpush