@extends('layouts.app')

@section('title', 'Conciliações Bancárias')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-balance-scale text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Conciliações Bancárias</h2>
        <p class="text-muted mb-0">Gerencie e monitore suas conciliações</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fas fa-filter me-2"></i>Filtros
    </button>
    <a href="{{ route('reconciliations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nova Conciliação
    </a>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="container-fluid">
        <!-- Cards de Resumo -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="bc-stat-card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total de Conciliações</h6>
                                <h3 class="mb-0">{{ $reconciliations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reconciliations->total() : $reconciliations->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-list-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bc-stat-card text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Aprovadas</h6>
                                <h3 class="mb-0">{{ $reconciliations->where('status', 'approved')->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bc-stat-card text-white" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Pendentes</h6>
                                <h3 class="mb-0">{{ $reconciliations->whereIn('status', ['draft', 'completed'])->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bc-stat-card text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Com Divergências</h6>
                                <h3 class="mb-0">{{ $reconciliations->filter(fn($r) => !$r->isBalanced())->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela Principal -->
        <div class="bc-section">
            <h2 class="bc-title">
                <i class="fas fa-list me-2 text-primary"></i>Lista de Conciliações
            </h2>
            
            <div class="bc-card">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar conciliações..." id="searchInput">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="bc-table table table-hover mb-0">
                            <thead>
                                <tr>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-hashtag me-2 text-muted"></i>ID
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-university me-2 text-muted"></i>Conta Bancária
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar me-2 text-muted"></i>Período
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <i class="fas fa-arrow-up me-2 text-success"></i>Saldo Inicial
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <i class="fas fa-arrow-down me-2 text-info"></i>Saldo Final
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <i class="fas fa-equals me-2 text-warning"></i>Diferença
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-info-circle me-2 text-muted"></i>Status
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-2 text-muted"></i>Criado por
                            </div>
                        </th>
                        <th class="border-0 px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $reconciliation)
                    <tr class="border-0">
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-primary fs-6">#{{ str_pad($reconciliation->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <strong class="text-dark">{{ $reconciliation->bankAccount->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $reconciliation->bankAccount->bank_name ?? 'Banco não informado' }}</small>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-2">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="small">
                                    <div><strong>{{ $reconciliation->start_date->format('d/m/Y') }}</strong></div>
                                    <div class="text-muted">até {{ $reconciliation->end_date->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <span class="fw-bold text-success">
                                R$ {{ number_format($reconciliation->starting_balance, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <span class="fw-bold text-info">
                                R$ {{ number_format($reconciliation->ending_balance, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            @php
                                $difference = $reconciliation->difference ?? 0;
                                $isBalanced = $reconciliation->isBalanced();
                            @endphp
                            <span class="fw-bold {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                                @if($isBalanced)
                                    <i class="fas fa-check-circle me-1"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                @endif
                                R$ {{ number_format(abs($difference), 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusConfig = [
                                    'approved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Aprovada'],
                                    'completed' => ['class' => 'info', 'icon' => 'clock', 'text' => 'Completa'],
                                    'draft' => ['class' => 'warning', 'icon' => 'edit', 'text' => 'Rascunho']
                                ];
                                $config = $statusConfig[$reconciliation->status] ?? $statusConfig['draft'];
                            @endphp
                            <span class="badge bg-{{ $config['class'] }} fs-6">
                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                {{ $config['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-white small"></i>
                                </div>
                                <div class="small">
                                    <div class="fw-bold">{{ $reconciliation->creator->name ?? 'Sistema' }}</div>
                                    <div class="text-muted">{{ $reconciliation->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('reconciliations.show', $reconciliation) }}" 
                                   class="btn btn-primary btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Visualizar Detalhes">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                                @if($reconciliation->status != 'approved')
                                <a href="#" class="btn btn-warning btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Editar Conciliação">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                                @endif
                                <a href="#" class="btn btn-info btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="Exportar Relatório">
                                    <i class="fas fa-download me-1"></i>Relatório
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-5 text-center">
                            <div class="py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma conciliação encontrada</h5>
                                <p class="text-muted mb-3">Comece criando sua primeira conciliação bancária</p>
                                <a href="{{ route('reconciliations.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nova Conciliação
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reconciliations instanceof \Illuminate\Pagination\LengthAwarePaginator && $reconciliations->hasPages())
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Mostrando {{ $reconciliations->firstItem() }} a {{ $reconciliations->lastItem() }} 
                de {{ $reconciliations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reconciliations->total() : $reconciliations->count() }} resultados
            </div>
            {{ $reconciliations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reconciliations->links() : '' }}
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
                            <option value="draft">Rascunho</option>
                            <option value="completed">Completa</option>
                            <option value="approved">Aprovada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conta Bancária</label>
                        <select class="form-select" name="bank_account">
                            <option value="">Todas as contas</option>
                            <!-- Opções serão populadas via JavaScript -->
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" name="date_from">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Data Final</label>
                                <input type="date" class="form-control" name="date_to">
                            </div>
                        </div>
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
        </div>
    </div>
</div>

@endpush
