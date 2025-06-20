@extends('layouts.app')

@section('title', 'Relatório de Transações')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Relatório de Transações
        </h1>
        <p class="text-muted mb-0">Análise detalhada das movimentações financeiras</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
            <li class="breadcrumb-item active">Transações</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<!-- Filtros Avançados -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros do Relatório
            </h5>
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                <i class="fas fa-cog me-1"></i>
                Filtros Avançados
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.transactions') }}" class="row g-3" id="filtersForm">
            <!-- Filtros Básicos -->
            <div class="col-md-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-university text-primary me-2"></i>
                    Conta Bancária
                </label>
                <select name="bank_account_id" class="form-select">
                    <option value="">🏦 Todas as contas</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} - {{ $account->bank_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-tags text-info me-2"></i>
                    Categoria
                </label>
                <select name="category" class="form-select">
                    <option value="">🏷️ Todas as categorias</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-bold">
                    <i class="fas fa-exchange-alt text-success me-2"></i>
                    Tipo
                </label>
                <select name="type" class="form-select">
                    <option value="">💰 Todos</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>📈 Crédito</option>
                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>📉 Débito</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-bold">
                    <i class="fas fa-calendar text-warning me-2"></i>
                    Data Inicial
                </label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-bold">
                    <i class="fas fa-calendar-check text-warning me-2"></i>
                    Data Final
                </label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <!-- Filtros Avançados (Colapsável) -->
            <div class="collapse" id="advancedFilters">
                <div class="row g-3 mt-3 pt-3 border-top">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>
                            <option value="reconciled" {{ request('status') == 'reconciled' ? 'selected' : '' }}>Conciliado</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Valor Mínimo</label>
                        <input type="number" name="min_amount" class="form-control" step="0.01" value="{{ request('min_amount') }}" placeholder="R$ 0,00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Valor Máximo</label>
                        <input type="number" name="max_amount" class="form-control" step="0.01" value="{{ request('max_amount') }}" placeholder="R$ 0,00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Buscar na Descrição</label>
                        <input type="text" name="description" class="form-control" value="{{ request('description') }}" placeholder="Buscar por descrição">
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('reports.transactions') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Limpar
                        </a>
                    </div>
                    
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel me-2"></i>
                            Excel
                        </button>
                        <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>
                            PDF
                        </button>
                        <button type="button" class="btn btn-info" onclick="exportReport('csv')">
                            <i class="fas fa-file-csv me-2"></i>
                            CSV
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card bg-primary text-white">
            <div class="stats-icon">
                <i class="fas fa-list-ol"></i>
            </div>
            <div class="stats-content">
                <h6 class="stats-title">Total de Transações</h6>
                <h2 class="stats-value">{{ number_format($summary['count'] ?? 0) }}</h2>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up me-1"></i>
                    Registros encontrados
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card bg-success text-white">
            <div class="stats-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="stats-content">
                <h6 class="stats-title">Total de Créditos</h6>
                <h2 class="stats-value">R$ {{ number_format($summary['total_credit'] ?? 0, 2, ',', '.') }}</h2>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up me-1"></i>
                    Entradas
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card bg-danger text-white">
            <div class="stats-icon">
                <i class="fas fa-minus-circle"></i>
            </div>
            <div class="stats-content">
                <h6 class="stats-title">Total de Débitos</h6>
                <h2 class="stats-value">R$ {{ number_format($summary['total_debit'] ?? 0, 2, ',', '.') }}</h2>
                <div class="stats-trend">
                    <i class="fas fa-arrow-down me-1"></i>
                    Saídas
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card bg-{{ ($summary['balance'] ?? 0) >= 0 ? 'info' : 'warning' }} text-white">
            <div class="stats-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stats-content">
                <h6 class="stats-title">Saldo Líquido</h6>
                <h2 class="stats-value">R$ {{ number_format($summary['balance'] ?? 0, 2, ',', '.') }}</h2>
                <div class="stats-trend">
                    <i class="fas fa-{{ ($summary['balance'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    {{ ($summary['balance'] ?? 0) >= 0 ? 'Positivo' : 'Negativo' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Transações -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table text-secondary me-2"></i>
                Transações Encontradas
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-info me-2">{{ $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transactions->total() : $transactions->count() ?? 0 }} registros</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="checkbox" class="btn-check" id="selectAll" autocomplete="off">
                    <label class="btn btn-outline-primary" for="selectAll" title="Selecionar Todos">
                        <i class="fas fa-check-square"></i>
                    </label>
                    <button class="btn btn-outline-secondary" onclick="toggleView()" title="Alternar Vista">
                        <i class="fas fa-list" id="viewToggleIcon"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="transactionsTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAllHeader" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-calendar text-primary me-1"></i>
                            Data
                        </th>
                        <th>
                            <i class="fas fa-university text-success me-1"></i>
                            Conta
                        </th>
                        <th>
                            <i class="fas fa-align-left text-info me-1"></i>
                            Descrição
                        </th>
                        <th>
                            <i class="fas fa-tags text-warning me-1"></i>
                            Categoria
                        </th>
                        <th>
                            <i class="fas fa-exchange-alt text-secondary me-1"></i>
                            Tipo
                        </th>
                        <th>
                            <i class="fas fa-dollar-sign text-success me-1"></i>
                            Valor
                        </th>
                        <th>
                            <i class="fas fa-flag text-danger me-1"></i>
                            Status
                        </th>
                        <th width="80">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr class="transaction-row" data-id="{{ $transaction->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input transaction-checkbox" value="{{ $transaction->id }}">
                        </td>
                        <td>
                            <span class="fw-bold">{{ $transaction->transaction_date->format('d/m/Y') }}</span>
                            <small class="d-block text-muted">{{ $transaction->transaction_date->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="account-avatar me-2">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>
                                    <span class="fw-bold">{{ $transaction->bankAccount->name }}</span>
                                    <small class="d-block text-muted">{{ $transaction->bankAccount->bank_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="description-wrapper">
                                <span class="description-text">{{ Str::limit($transaction->description, 50) }}</span>
                                @if(strlen($transaction->description) > 50)
                                    <button class="btn btn-sm btn-link p-0 ms-1" onclick="toggleDescription(this)" title="Ver descrição completa">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="full-description d-none">{{ $transaction->description }}</div>
                        </td>
                        <td>
                            @if($transaction->category)
                                <span class="badge bg-secondary">
                                    <i class="fas fa-tag me-1"></i>
                                    {{ $transaction->category }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-question me-1"></i>
                                    Sem categoria
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $transaction->type == 'credit' ? 'success' : 'danger' }} fs-6">
                                <i class="fas fa-{{ $transaction->type == 'credit' ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                {{ $transaction->type == 'credit' ? 'Entrada' : 'Saída' }}
                            </span>
                        </td>
                        <td>
                            <span class="amount {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $transaction->formatted_amount }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $transaction->status == 'reconciled' ? 'success' : 'warning' }}">
                                <i class="fas fa-{{ $transaction->status == 'reconciled' ? 'check' : 'clock' }} me-1"></i>
                                {{ $transaction->status == 'reconciled' ? 'Conciliado' : 'Pendente' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary" onclick="viewTransaction({{ $transaction->id }})" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="editTransaction({{ $transaction->id }})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                                <p class="text-muted">Tente ajustar os filtros para encontrar as transações desejadas</p>
                                <a href="{{ route('reports.transactions') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-refresh me-2"></i>
                                    Limpar Filtros
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Paginação -->
    @if(isset($transactions) && method_exists($transactions, 'hasPages') && $transactions->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                <small class="text-muted">
                    Mostrando {{ $transactions->firstItem() }} a {{ $transactions->lastItem() }} 
                    de {{ $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transactions->total() : $transactions->count() }} transações
                </small>
            </div>
            <div class="pagination-links">
                {{ $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transactions->links() : '' }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Ações em Lote -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tasks me-2"></i>
                    Ações em Lote
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Selecione a ação desejada para as <span id="selectedCount">0</span> transações selecionadas:</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" onclick="bulkAction('reconcile')">
                        <i class="fas fa-check me-2"></i>
                        Marcar como Conciliadas
                    </button>
                    <button class="btn btn-warning" onclick="bulkAction('pending')">
                        <i class="fas fa-clock me-2"></i>
                        Marcar como Pendentes
                    </button>
                    <button class="btn btn-info" onclick="bulkAction('export')">
                        <i class="fas fa-download me-2"></i>
                        Exportar Selecionadas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    border-radius: 15px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    min-height: 120px;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-icon {
    font-size: 2.5rem;
    margin-right: 20px;
    opacity: 0.8;
}

.stats-content {
    flex: 1;
}

.stats-title {
    font-size: 0.9rem;
    margin-bottom: 8px;
    opacity: 0.9;
}

.stats-value {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stats-trend {
    font-size: 0.8rem;
    opacity: 0.8;
}

.account-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--bs-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.description-wrapper {
    position: relative;
}

.amount {
    font-size: 1.1rem;
}

.transaction-row {
    transition: all 0.2s ease;
}

.transaction-row:hover {
    background-color: var(--bs-gray-50);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.transaction-row.selected {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-left: 4px solid var(--bs-primary);
}

.empty-state {
    padding: 40px 20px;
}

.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

@media (max-width: 768px) {
    .stats-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-icon {
        margin-right: 0;
        margin-bottom: 15px;
        font-size: 2rem;
    }
    
    .stats-value {
        font-size: 1.5rem;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .account-avatar {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleção de transações
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    
    function updateSelection() {
        const selectedCount = document.querySelectorAll('.transaction-checkbox:checked').length;
        const selectedCountSpan = document.getElementById('selectedCount');
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedCount;
        }
        
        // Atualizar visual das linhas selecionadas
        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (checkbox.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
        
        // Mostrar/ocultar botão de ações em lote
        const bulkActionsBtn = document.getElementById('bulkActionsBtn');
        if (bulkActionsBtn) {
            bulkActionsBtn.style.display = selectedCount > 0 ? 'inline-block' : 'none';
        }
    }
    
    // Selecionar todos
    [selectAllHeader, selectAll].forEach(checkbox => {
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                updateSelection();
            });
        }
    });
    
    // Seleção individual
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });
    
    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'a':
                    e.preventDefault();
                    selectAllHeader.checked = true;
                    selectAllHeader.dispatchEvent(new Event('change'));
                    break;
                case 'e':
                    e.preventDefault();
                    exportReport('excel');
                    break;
                case 'p':
                    e.preventDefault();
                    exportReport('pdf');
                    break;
            }
        }
    });
    
    // Auto-aplicar filtros quando mudar período
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('filtersForm').submit();
            }
        });
    });
});

function toggleDescription(button) {
    const row = button.closest('tr');
    const shortDesc = row.querySelector('.description-text');
    const fullDesc = row.querySelector('.full-description');
    const icon = button.querySelector('i');
    
    if (fullDesc.classList.contains('d-none')) {
        shortDesc.classList.add('d-none');
        fullDesc.classList.remove('d-none');
        icon.className = 'fas fa-compress-alt';
        button.title = 'Ver descrição resumida';
    } else {
        shortDesc.classList.remove('d-none');
        fullDesc.classList.add('d-none');
        icon.className = 'fas fa-expand-alt';
        button.title = 'Ver descrição completa';
    }
}

function toggleView() {
    const table = document.getElementById('transactionsTable');
    const icon = document.getElementById('viewToggleIcon');
    
    if (table.classList.contains('compact-view')) {
        table.classList.remove('compact-view');
        icon.className = 'fas fa-list';
    } else {
        table.classList.add('compact-view');
        icon.className = 'fas fa-th';
    }
}

function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    const url = window.location.pathname + '?' + params.toString();
    window.open(url, '_blank');
}

function viewTransaction(id) {
    window.location.href = `/transactions/${id}`;
}

function editTransaction(id) {
    window.location.href = `/transactions/${id}/edit`;
}

function bulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.transaction-checkbox:checked'))
                            .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Selecione pelo menos uma transação');
        return;
    }
    
    // Implementar ação em lote
    console.log(`Executando ação "${action}" para IDs:`, selectedIds);
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal'));
    modal.hide();
}
</script>
@endsection
