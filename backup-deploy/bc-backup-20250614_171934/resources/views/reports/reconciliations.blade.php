@extends('layouts.app')

@section('title', 'Relatório de Conciliações Bancárias')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-balance-scale text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Relatório de Conciliações Bancárias</h2>
        <p class="text-muted mb-0">Análise completa das conciliações realizadas</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-success" onclick="exportData('excel')">
        <i class="fas fa-file-excel me-2"></i>Excel
    </button>
    <button class="btn btn-outline-danger" onclick="exportData('pdf')">
        <i class="fas fa-file-pdf me-2"></i>PDF
    </button>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimir
    </button>
</div>
@endsection

@section('content')
<!-- Filtros Avançados -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filtros e Período de Análise
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.reconciliations') }}" id="reconciliationFilters">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-university text-primary me-2"></i>Conta Bancária
                    </label>
                    <select name="bank_account_id" class="form-select form-select-lg">
                        <option value="">Todas as contas</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} - {{ $account->bank_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-info-circle text-info me-2"></i>Status
                    </label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Todos os status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                            <i class="fas fa-edit"></i> Rascunho
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            <i class="fas fa-check"></i> Completa
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                            <i class="fas fa-thumbs-up"></i> Aprovada
                        </option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-alt text-success me-2"></i>Data Inicial
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-lg" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-check text-info me-2"></i>Data Final
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-lg" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-balance-scale text-warning me-2"></i>Situação
                    </label>
                    <select name="balanced" class="form-select form-select-lg">
                        <option value="">Todas</option>
                        <option value="1" {{ request('balanced') == '1' ? 'selected' : '' }}>Balanceadas</option>
                        <option value="0" {{ request('balanced') == '0' ? 'selected' : '' }}>Com Divergências</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user text-secondary me-2"></i>Criado por
                    </label>
                    <input type="text" name="creator" class="form-control form-control-lg" 
                           value="{{ request('creator') }}" placeholder="Nome do usuário...">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ações Rápidas</label>
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickFilter('today')">
                            Hoje
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickFilter('week')">
                            Esta Semana
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickFilter('month')">
                            Este Mês
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setQuickFilter('quarter')">
                            Trimestre
                        </button>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('reports.reconciliations') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Limpar Filtros
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-list-alt fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted">Total de Conciliações</h6>
                <h2 class="text-primary mb-0">{{ $stats['total'] }}</h2>
                <small class="text-muted">No período</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h6 class="text-muted">Aprovadas</h6>
                <h2 class="text-success mb-0">{{ $stats['approved'] }}</h2>
                <small class="text-muted">
                    {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0 }}% do total
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h6 class="text-muted">Pendentes</h6>
                <h2 class="text-warning mb-0">{{ $stats['pending'] }}</h2>
                <small class="text-muted">
                    {{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0 }}% do total
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-balance-scale fa-2x text-info"></i>
                </div>
                <h6 class="text-muted">Balanceadas</h6>
                <h2 class="text-info mb-0">{{ $stats['balanced'] }}</h2>
                <small class="text-muted">
                    {{ $stats['total'] > 0 ? round(($stats['balanced'] / $stats['total']) * 100) : 0 }}% do total
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Conciliações -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table text-primary me-2"></i>Lista de Conciliações
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#tableControls">
                    <i class="fas fa-cog me-2"></i>Controles
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sort me-2"></i>Ordenar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="sortTable('date')">Por Data</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable('account')">Por Conta</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable('status')">Por Status</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable('balance')">Por Saldo</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="collapse mt-3" id="tableControls">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="searchTable" 
                           placeholder="Buscar conciliações...">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="filterStatus">
                        <option value="">Todos status</option>
                        <option value="approved">Aprovadas</option>
                        <option value="completed">Completas</option>
                        <option value="draft">Rascunhos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="filterBalanced">
                        <option value="">Todos</option>
                        <option value="balanced">Balanceadas</option>
                        <option value="unbalanced">Com Divergência</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="minValue" placeholder="Valor mín">
                        <input type="number" class="form-control" id="maxValue" placeholder="Valor máx">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-secondary w-100" onclick="clearTableFilters()">
                        Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reconciliationsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-hashtag me-2 text-muted"></i>ID
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-university me-2 text-muted"></i>Conta Bancária
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-calendar me-2 text-muted"></i>Período
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <i class="fas fa-arrow-up me-2 text-success"></i>Saldo Inicial
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <i class="fas fa-arrow-down me-2 text-info"></i>Saldo Final
                        </th>
                        <th class="border-0 px-4 py-3 text-end">
                            <i class="fas fa-balance-scale me-2 text-warning"></i>Diferença
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <i class="fas fa-info-circle me-2 text-muted"></i>Status
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-user me-2 text-muted"></i>Criado por
                        </th>
                        <th class="border-0 px-4 py-3">
                            <i class="fas fa-clock me-2 text-muted"></i>Data
                        </th>
                        <th class="border-0 px-4 py-3 text-center">
                            <i class="fas fa-cog me-2 text-muted"></i>Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $reconciliation)
                    <tr class="reconciliation-row">
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark fs-6">
                                #{{ str_pad($reconciliation->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="fas fa-university text-primary"></i>
                                </div>
                                <div>
                                    <strong>{{ $reconciliation->bankAccount->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $reconciliation->bankAccount->bank_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="small">
                                <strong>{{ $reconciliation->start_date->format('d/m/Y') }}</strong>
                                <span class="text-muted">até</span>
                                <strong>{{ $reconciliation->end_date->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $reconciliation->start_date->diffInDays($reconciliation->end_date) + 1 }} dias
                                </small>
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
                            @php $difference = $reconciliation->difference ?? 0; @endphp
                            <span class="fw-bold {{ $reconciliation->isBalanced() ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $reconciliation->isBalanced() ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
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
                                <div class="bg-{{ $config['class'] }} bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-user text-{{ $config['class'] }} small"></i>
                                </div>
                                <div class="small">
                                    <strong>{{ $reconciliation->creator->name ?? 'Sistema' }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="small">
                                <strong>{{ $reconciliation->created_at->format('d/m/Y') }}</strong>
                                <br>
                                <span class="text-muted">{{ $reconciliation->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reconciliations.show', $reconciliation) }}" 
                                   class="btn btn-outline-primary" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($reconciliation->status !== 'approved')
                                    <a href="{{ route('reconciliations.edit', $reconciliation) }}" 
                                       class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" 
                                            data-bs-toggle="dropdown" title="Mais opções">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('reconciliations.report', $reconciliation) }}">
                                                <i class="fas fa-file-pdf me-2"></i>Relatório PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="duplicateReconciliation({{ $reconciliation->id }})">
                                                <i class="fas fa-copy me-2"></i>Duplicar
                                            </a>
                                        </li>
                                        @if($reconciliation->status !== 'approved')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" 
                                                   onclick="deleteReconciliation({{ $reconciliation->id }})">
                                                    <i class="fas fa-trash me-2"></i>Excluir
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-5 text-center">
                            <div class="py-4">
                                <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma conciliação encontrada</h5>
                                <p class="text-muted mb-0">
                                    Não há conciliações que correspondam aos filtros selecionados.
                                </p>
                                <a href="{{ route('reconciliations.create') }}" class="btn btn-outline-primary mt-3">
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
        <div class="card-footer bg-white border-top">
            {{ $reconciliations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reconciliations->links() : '' }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.reconciliation-row:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.875em;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .px-4 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Filtros da tabela
    $('#searchTable, #filterStatus, #filterBalanced, #minValue, #maxValue').on('change keyup', function() {
        filterTable();
    });
    
    // Atalhos de teclado
    $(document).on('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.which) {
                case 70: // Ctrl+F - Focar na busca
                    e.preventDefault();
                    $('#searchTable').focus();
                    break;
                case 78: // Ctrl+N - Nova conciliação
                    e.preventDefault();
                    window.location.href = '{{ route("reconciliations.create") }}';
                    break;
            }
        }
    });
});

function setQuickFilter(period) {
    const today = new Date();
    let startDate, endDate = today;
    
    switch(period) {
        case 'today':
            startDate = today;
            break;
        case 'week':
            startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            break;
    }
    
    $('input[name="date_from"]').val(startDate.toISOString().split('T')[0]);
    $('input[name="date_to"]').val(endDate.toISOString().split('T')[0]);
}

function filterTable() {
    const searchTerm = $('#searchTable').val().toLowerCase();
    const statusFilter = $('#filterStatus').val();
    const balancedFilter = $('#filterBalanced').val();
    const minValue = parseFloat($('#minValue').val()) || 0;
    const maxValue = parseFloat($('#maxValue').val()) || Infinity;
    
    $('.reconciliation-row').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const status = row.find('.badge').text().toLowerCase();
        const isBalanced = row.find('.text-success').length > 0;
        const balanceValue = parseFloat(row.find('td:nth-child(5)').text().replace(/[^\d,-]/g, '').replace(',', '.'));
        
        let show = true;
        
        if (searchTerm && !text.includes(searchTerm)) show = false;
        if (statusFilter && !status.includes(statusFilter)) show = false;
        if (balancedFilter === 'balanced' && !isBalanced) show = false;
        if (balancedFilter === 'unbalanced' && isBalanced) show = false;
        if (balanceValue < minValue || balanceValue > maxValue) show = false;
        
        row.toggle(show);
    });
}

function clearTableFilters() {
    $('#searchTable, #minValue, #maxValue').val('');
    $('#filterStatus, #filterBalanced').val('');
    $('.reconciliation-row').show();
}

function sortTable(field) {
    // Implementar ordenação
    console.log('Ordenando por:', field);
}

function exportData(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    window.open(`{{ route('reports.reconciliations') }}?${params.toString()}`, '_blank');
}

function duplicateReconciliation(id) {
    if (confirm('Deseja criar uma nova conciliação baseada nesta?')) {
        window.location.href = `{{ url('reconciliations/create') }}?duplicate=${id}`;
    }
}

function deleteReconciliation(id) {
    if (confirm('Tem certeza que deseja excluir esta conciliação? Esta ação não pode ser desfeita.')) {
        // Implementar exclusão via AJAX ou form
        console.log('Excluindo conciliação:', id);
    }
}
</script>
@endpush
