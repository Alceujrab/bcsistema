@extends('layouts.app')

@section('title', 'Categorias')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags text-warning me-2"></i>
            Categorias
        </h1>
        <p class="text-muted mb-0">Organize suas transações por categorias personalizadas</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nova Categoria
        </a>
        <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importCategoriesModal">
                <i class="fas fa-upload me-2"></i>Importar Categorias
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="exportCategories()">
                <i class="fas fa-download me-2"></i>Exportar Lista
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#helpModal">
                <i class="fas fa-question-circle me-2"></i>Ajuda
            </a></li>
        </ul>
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
                            Total de Categorias
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->total() : $categories->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                            Categorias Ativas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $categories->where('active', true)->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Receitas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $categories->where('type', 'income')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Despesas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $categories->where('type', 'expense')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Busca -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search me-2"></i>Buscar e Filtrar
        </h6>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search text-primary me-1"></i>Buscar Nome
                    </label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Digite o nome da categoria...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-filter text-info me-1"></i>Tipo
                    </label>
                    <select class="form-select" id="typeFilter">
                        <option value="">📋 Todos os tipos</option>
                        <option value="income">💰 Receitas</option>
                        <option value="expense">💸 Despesas</option>
                        <option value="both">🔄 Ambos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-toggle-on text-success me-1"></i>Status
                    </label>
                    <select class="form-select" id="statusFilter">
                        <option value="">🏷️ Todos os status</option>
                        <option value="active">✅ Ativas</option>
                        <option value="inactive">❌ Inativas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Ações</label>
                    <div class="d-grid">
                        <button class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<<!-- Tabela de Categorias -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table me-2"></i>Lista de Categorias
        </h6>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-info" onclick="toggleSortMode()" data-bs-toggle="tooltip" title="Modo de Reordenação">
                <i class="fas fa-sort" id="sortIcon"></i>
            </button>
            <button class="btn btn-outline-secondary" onclick="expandAll()" data-bs-toggle="tooltip" title="Expandir Detalhes">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info" id="sortAlert" style="display: none;">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Modo de Reordenação:</strong> Arraste as linhas pelos ícones <i class="fas fa-grip-vertical"></i> para reorganizar as categorias.
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="categories-table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 50px;">
                            <i class="fas fa-grip-vertical text-muted" data-bs-toggle="tooltip" title="Arrastar para reordenar"></i>
                        </th>
                        <th><i class="fas fa-tag me-1"></i>Categoria</th>
                        <th><i class="fas fa-chart-line me-1"></i>Tipo</th>
                        <th><i class="fas fa-key me-1"></i>Palavras-chave</th>
                        <th><i class="fas fa-calculator me-1"></i>Transações</th>
                        <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                        <th class="text-center"><i class="fas fa-cogs me-1"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr data-id="{{ $category->id }}" class="category-row">
                        <td class="text-center">
                            <i class="fas fa-grip-vertical text-muted handle" style="cursor: move;" 
                               data-bs-toggle="tooltip" title="Arraste para reordenar"></i>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2 category-badge" 
                                      style="background-color: {{ $category->color }}; font-size: 14px;">
                                    <i class="fas {{ $category->icon ?? 'fa-tag' }}"></i>
                                </span>
                                <div>
                                    <strong>{{ $category->name }}</strong>
                                    @if($category->slug)
                                        <br><small class="text-muted">{{ $category->slug }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($category->type == 'income')
                                <span class="badge bg-success">
                                    <i class="fas fa-arrow-up me-1"></i>Receita
                                </span>
                            @elseif($category->type == 'expense')
                                <span class="badge bg-danger">
                                    <i class="fas fa-arrow-down me-1"></i>Despesa
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-exchange-alt me-1"></i>Ambos
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($category->keywords && count($category->keywords) > 0)
                                <div class="keywords-container">
                                    @foreach(array_slice($category->keywords, 0, 2) as $keyword)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $keyword }}</span>
                                    @endforeach
                                    @if(count($category->keywords) > 2)
                                        <span class="badge bg-info" data-bs-toggle="tooltip" 
                                              title="{{ implode(', ', array_slice($category->keywords, 2)) }}">
                                            +{{ count($category->keywords) - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-minus me-1"></i>Sem palavras-chave
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(($category->transactions_count ?? 0) > 0)
                                <span class="badge bg-primary">
                                    <i class="fas fa-list me-1"></i>
                                    {{ $category->transactions_count ?? 0 }}
                                </span>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-inbox me-1"></i>0
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($category->active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Ativa
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Inativa
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="btn btn-warning" data-bs-toggle="tooltip" title="Editar Categoria">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#detailsModal{{ $category->id }}"
                                        data-bs-toggle="tooltip" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if(($category->transactions_count ?? 0) == 0)
                                <button class="btn btn-danger" onclick="confirmDelete({{ $category->id }})" 
                                        data-bs-toggle="tooltip" title="Excluir Categoria">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button class="btn btn-outline-secondary" disabled 
                                        data-bs-toggle="tooltip" title="Não pode excluir - possui transações">
                                    <i class="fas fa-lock"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-tags fa-3x mb-3"></i>
                                <h5>Nenhuma categoria encontrada</h5>
                                <p>
                                    <a href="{{ route('categories.create') }}" class="text-decoration-none">
                                        Criar sua primeira categoria
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                @if(isset($categories) && (($categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->total() : $categories->count()) > 0))
                    Mostrando 
                    {{ method_exists($categories, 'firstItem') ? $categories->firstItem() : 1 }} 
                    até 
                    {{ method_exists($categories, 'lastItem') ? $categories->lastItem() : $categories->count() }} 
                    de {{ $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? number_format($categories->total()) : number_format($categories->count()) }} categorias
                @endif
            </div>
            <div>
                {{ isset($categories) && $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->links() : '' }}
            </div>
        </div>
    </div>
</div>

<!-- Modais de Detalhes das Categorias -->
@foreach($categories as $category)
<div class="modal fade" id="detailsModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $category->color }}; color: white;">
                <h5 class="modal-title">
                    <i class="fas {{ $category->icon ?? 'fa-tag' }} me-2"></i>
                    {{ $category->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle text-primary me-2"></i>Informações Básicas</h6>
                        <ul class="list-unstyled">
                            <li><strong>Tipo:</strong> 
                                @if($category->type == 'income')
                                    <span class="badge bg-success">💰 Receita</span>
                                @elseif($category->type == 'expense')
                                    <span class="badge bg-danger">💸 Despesa</span>
                                @else
                                    <span class="badge bg-secondary">🔄 Ambos</span>
                                @endif
                            </li>
                            <li><strong>Status:</strong> 
                                <span class="badge bg-{{ $category->active ? 'success' : 'danger' }}">
                                    {{ $category->active ? '✅ Ativa' : '❌ Inativa' }}
                                </span>
                            </li>
                            <li><strong>Slug:</strong> <code>{{ $category->slug }}</code></li>
                            <li><strong>Ordem:</strong> {{ $category->sort_order ?? 'N/A' }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-bar text-success me-2"></i>Estatísticas</h6>
                        <ul class="list-unstyled">
                            <li><strong>Transações:</strong> {{ $category->transactions_count ?? 0 }}</li>
                            <li><strong>Criada em:</strong> {{ $category->created_at->format('d/m/Y') }}</li>
                            <li><strong>Atualizada em:</strong> {{ $category->updated_at->format('d/m/Y') }}</li>
                        </ul>
                    </div>
                </div>
                
                @if($category->keywords && count($category->keywords) > 0)
                <hr>
                <h6><i class="fas fa-tags text-warning me-2"></i>Palavras-chave</h6>
                <div>
                    @foreach($category->keywords as $keyword)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $keyword }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal de Importação -->
<div class="modal fade" id="importCategoriesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Importar Categorias
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Formato esperado:</strong> Arquivo JSON com estrutura de categorias.
                </div>
                <form>
                    <div class="mb-3">
                        <label class="form-label">Arquivo JSON</label>
                        <input type="file" class="form-control" accept=".json">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="replaceExisting">
                        <label class="form-check-label" for="replaceExisting">
                            Substituir categorias existentes
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ajuda -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Ajuda - Gerenciamento de Categorias
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-tags text-primary me-2"></i>Sobre Categorias</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>Organize suas transações por tipo</li>
                            <li><i class="fas fa-check text-success me-1"></i>Facilite a geração de relatórios</li>
                            <li><i class="fas fa-check text-success me-1"></i>Acompanhe gastos por área</li>
                        </ul>
                        
                        <h6><i class="fas fa-sort text-info me-2"></i>Reordenação</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>Arraste pelo ícone <i class="fas fa-grip-vertical"></i></li>
                            <li><i class="fas fa-check text-success me-1"></i>Ordem personalizada nas listas</li>
                            <li><i class="fas fa-check text-success me-1"></i>Salvamento automático</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-key text-warning me-2"></i>Palavras-chave</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>Auto-categorização de transações</li>
                            <li><i class="fas fa-check text-success me-1"></i>Separe por vírgulas</li>
                            <li><i class="fas fa-check text-success me-1"></i>Ex: mercado, supermercado, feira</li>
                        </ul>
                        
                        <h6><i class="fas fa-palette text-secondary me-2"></i>Cores e Ícones</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i>Identificação visual rápida</li>
                            <li><i class="fas fa-check text-success me-1"></i>Organize por cores</li>
                            <li><i class="fas fa-check text-success me-1"></i>Ícones do Font Awesome</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar Sortable
    const tbody = document.querySelector('#categories-table tbody');
    let sortable = null;
    
    function initSortable() {
        if (sortable) {
            sortable.destroy();
        }
        
        sortable = new Sortable(tbody, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'table-active',
            onEnd: function(evt) {
                let positions = [];
                tbody.querySelectorAll('tr').forEach((row, index) => {
                    if (row.dataset.id) {
                        positions[index] = row.dataset.id;
                    }
                });
                
                // Mostrar feedback
                showToast('success', 'Ordem das categorias atualizada!');
                
                fetch('{{ route("categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ positions: positions })
                }).catch(error => {
                    console.error('Erro ao salvar ordem:', error);
                    showToast('error', 'Erro ao salvar a ordem das categorias');
                });
            }
        });
    }
    
    // Inicializar sortable
    initSortable();
    
    // Filtros em tempo real
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterTable();
    });
    
    $('#typeFilter, #statusFilter').on('change', function() {
        filterTable();
    });
    
    function filterTable() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const typeFilter = $('#typeFilter').val();
        const statusFilter = $('#statusFilter').val();
        
        $('.category-row').each(function() {
            const row = $(this);
            const name = row.find('td:nth-child(2)').text().toLowerCase();
            const type = row.find('td:nth-child(3) .badge').text().toLowerCase();
            const status = row.find('td:nth-child(6) .badge').text().toLowerCase();
            
            let visible = true;
            
            // Filtro de busca
            if (searchTerm && !name.includes(searchTerm)) {
                visible = false;
            }
            
            // Filtro de tipo
            if (typeFilter) {
                if (typeFilter === 'income' && !type.includes('receita')) visible = false;
                if (typeFilter === 'expense' && !type.includes('despesa')) visible = false;
                if (typeFilter === 'both' && !type.includes('ambos')) visible = false;
            }
            
            // Filtro de status
            if (statusFilter) {
                if (statusFilter === 'active' && !status.includes('ativa')) visible = false;
                if (statusFilter === 'inactive' && !status.includes('inativa')) visible = false;
            }
            
            row.toggle(visible);
        });
        
        // Mostrar mensagem se nenhum resultado
        const visibleRows = $('.category-row:visible').length;
        if (visibleRows === 0 && $('.no-results-row').length === 0) {
            $('#categories-table tbody').append(`
                <tr class="no-results-row">
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>Nenhuma categoria encontrada com os filtros aplicados.</p>
                    </td>
                </tr>
            `);
        } else if (visibleRows > 0) {
            $('.no-results-row').remove();
        }
    }
});

// Funções globais
function toggleSortMode() {
    const icon = $('#sortIcon');
    const alert = $('#sortAlert');
    
    if (alert.is(':visible')) {
        alert.slideUp();
        icon.removeClass('fa-check').addClass('fa-sort');
    } else {
        alert.slideDown();
        icon.removeClass('fa-sort').addClass('fa-check');
    }
}

function expandAll() {
    $('.keywords-container').each(function() {
        const container = $(this);
        const hiddenBadges = container.find('.badge:hidden');
        
        if (hiddenBadges.length > 0) {
            hiddenBadges.show();
            container.find('.badge.bg-info').hide();
        }
    });
}

function clearFilters() {
    $('#searchInput').val('');
    $('#typeFilter').val('');
    $('#statusFilter').val('');
    $('.category-row').show();
    $('.no-results-row').remove();
    showToast('info', 'Filtros limpos!');
}

function exportCategories() {
    // Simular exportação
    showToast('info', 'Exportando categorias...');
    
    setTimeout(() => {
        const data = {
            categories: [],
            exported_at: new Date().toISOString(),
            total: $('.category-row:visible').length
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], {
            type: 'application/json'
        });
        
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'categorias-' + new Date().toISOString().slice(0, 10) + '.json';
        a.click();
        
        URL.revokeObjectURL(url);
        showToast('success', 'Categorias exportadas com sucesso!');
    }, 1000);
}

function confirmDelete(categoryId) {
    if (confirm('Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.')) {
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("categories.destroy", ":id") }}'.replace(':id', categoryId)
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_method',
            value: 'DELETE'
        }));
        
        $('body').append(form);
        form.submit();
    }
}

function showToast(type, message) {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    // Adicionar container de toasts se não existir
    if ($('#toast-container').length === 0) {
        $('body').append('<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>');
    }
    
    $('#toast-container').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Remover após 5 segundos
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Highlight de linhas na tabela
$('.category-row').hover(
    function() { $(this).addClass('table-active'); },
    function() { $(this).removeClass('table-active'); }
);
</script>
@endpush
