@extends('layouts.app')

@section('title', 'Gestão: ' . $account->name)

@section('header')
<div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="{{ route('account-management.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex align-items-center">
            @switch($account->type)
                @case('checking')
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-university text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    @break
                @case('savings')
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-piggy-bank text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    @break
                @case('credit_card')
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-credit-card text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    @break
                @case('investment')
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-chart-line text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    @break
            @endswitch
            <div>
                <h2 class="mb-0">{{ $account->name }}</h2>
                <p class="text-muted mb-0">{{ $account->bank_name }} • {{ $account->type_name }}</p>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="showTransferModal()">
            <i class="fas fa-exchange-alt me-2"></i>Transferir Lançamentos
        </button>
        <a href="{{ route('bank-accounts.edit', $account) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="{{ route('reconciliations.create', ['bank_account_id' => $account->id]) }}" class="btn btn-primary">
            <i class="fas fa-balance-scale me-2"></i>Nova Conciliação
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Resumo da Conta -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-wallet text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                    R$ {{ number_format($account->balance, 2, ',', '.') }}
                </h3>
                <p class="text-muted mb-0">Saldo Atual</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-arrow-up text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-success">R$ {{ number_format($stats['total_credit'], 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Entradas</p>
                <small class="text-muted">Média: R$ {{ number_format($stats['average_credit'], 2, ',', '.') }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-arrow-down text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-danger">R$ {{ number_format($stats['total_debit'], 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total Saídas</p>
                <small class="text-muted">Média: R$ {{ number_format($stats['average_debit'], 2, ',', '.') }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-list text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h3 class="mb-1 text-info">{{ $stats['total_transactions'] }}</h3>
                <p class="text-muted mb-0">Total Transações</p>
                <small class="text-muted">
                    <span class="text-warning">{{ $stats['pending_count'] }} pendentes</span> • 
                    <span class="text-success">{{ $stats['reconciled_count'] }} conciliadas</span>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico Mensal e Top Categorias -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Movimentação Mensal (Últimos 6 Meses)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Top Categorias
                </h5>
            </div>
            <div class="card-body">
                @forelse($topCategories as $category)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle" style="width: 12px; height: 12px; background-color: {{ $category->color ?? '#6c757d' }};"></div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->transaction_count }} transações</small>
                        </div>
                        <div class="text-end">
                            <strong>R$ {{ number_format($category->total_amount, 2, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Nenhuma categoria encontrada</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Lista de Transações -->
<div class="row">
    <div class="col-12">
        <!-- Filtros -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3" id="filterForm">
                    <div class="col-md-2">
                        <label for="category_id" class="form-label">Categoria</label>
                        <select name="category_id" id="category_id" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label">Tipo</label>
                        <select name="type" id="type" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Entrada</option>
                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Saída</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="reconciled" {{ request('status') == 'reconciled' ? 'selected' : '' }}>Conciliado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Data Inicial</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Data Final</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="search" class="form-label">Buscar</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Descrição..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('account-management.show', $account) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </a>
                            @if(request()->hasAny(['category_id', 'type', 'status', 'date_from', 'date_to', 'search']))
                                <span class="badge bg-info">
                                    <i class="fas fa-filter me-1"></i>
                                    Filtros ativos
                                </span>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Transações
                    @if($transactions->total() > 0)
                        <span class="badge bg-secondary ms-2">{{ $transactions->total() }}</span>
                    @endif
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="selectAllTransactions()">
                        <i class="fas fa-check-square me-1"></i>Selecionar Todas
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="transferSelectedTransactions()">
                        <i class="fas fa-exchange-alt me-1"></i>Transferir Selecionadas
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" class="form-check-input" id="selectAllTrans">
                                    </th>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input transaction-checkbox" 
                                               value="{{ $transaction->id }}">
                                    </td>
                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $transaction->description }}</h6>
                                            @if($transaction->notes)
                                                <small class="text-muted">{{ Str::limit($transaction->notes, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($transaction->category) && is_object($transaction->category) && !empty($transaction->category->name))
                                            <span class="badge" style="background-color: {{ $transaction->category->color ?? '#6c757d' }}">
                                                {{ $transaction->category->name }}
                                            </span>
                                        @elseif(!empty($transaction->category_id))
                                            <span class="badge bg-secondary">
                                                Categoria #{{ $transaction->category_id }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sem categoria</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                            {{ $transaction->type == 'credit' ? '+' : '-' }}R$ {{ number_format(abs($transaction->amount), 2, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        @switch($transaction->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pendente</span>
                                                @break
                                            @case('reconciled')
                                                <span class="badge bg-success">Conciliada</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelada</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transactions.show', $transaction) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="transferSingleTransaction({{ $transaction->id }})" title="Transferir">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <div class="card-footer bg-white border-0">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Nenhuma transação encontrada</h5>
                        <p class="text-muted">Esta conta ainda não possui transações</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Transferência -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transferir Transações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    @csrf
                    <div class="mb-3">
                        <label for="target_account_id" class="form-label">Conta de Destino</label>
                        <select class="form-select" id="target_account_id" name="target_account_id" required>
                            <option value="">Selecione a conta...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transfer_notes" class="form-label">Observações (opcional)</label>
                        <textarea class="form-control" id="transfer_notes" name="notes" rows="3" 
                                  placeholder="Motivo da transferência..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="selectedCount">0</span> transação(ões) selecionada(s) para transferência.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="executeTransfer()">Transferir</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.transaction-checkbox:checked + td {
    background-color: rgba(13, 110, 253, 0.1);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Gráfico mensal
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($monthlyStats);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Entradas',
                data: monthlyData.map(item => item.credit),
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Saídas',
                data: monthlyData.map(item => item.debit),
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        });
    
    // Checkboxes
    $('#selectAllTrans').change(function() {
        $('.transaction-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });
    
    $('.transaction-checkbox').change(function() {
        updateSelectedCount();
        if (!$(this).is(':checked')) {
            $('#selectAllTrans').prop('checked', false);
        } else if ($('.transaction-checkbox:checked').length === $('.transaction-checkbox').length) {
            $('#selectAllTrans').prop('checked', true);
        }
    });
});

// Auto-submit dos filtros
document.addEventListener('DOMContentLoaded', function() {
    // Elementos dos filtros
    const filterForm = document.getElementById('filterForm');
    const categorySelect = document.getElementById('category_id');
    const typeSelect = document.getElementById('type');
    const statusSelect = document.getElementById('status');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');

    // Auto-submit quando selects mudarem
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Auto-submit quando datas mudarem (com delay)
    let dateTimeout;
    function handleDateChange() {
        clearTimeout(dateTimeout);
        dateTimeout = setTimeout(function() {
            if (dateFromInput.value && dateToInput.value) {
                filterForm.submit();
            }
        }, 500); // 500ms de delay
    }

    if (dateFromInput) {
        dateFromInput.addEventListener('change', handleDateChange);
    }

    if (dateToInput) {
        dateToInput.addEventListener('change', handleDateChange);
    }

    // Enter no campo de busca
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});

function updateSelectedCount() {
    const count = $('.transaction-checkbox:checked').length;
    $('#selectedCount').text(count);
}

function selectAllTransactions() {
    $('.transaction-checkbox').prop('checked', true);
    $('#selectAllTrans').prop('checked', true);
    updateSelectedCount();
}

function showTransferModal() {
    const selectedTransactions = $('.transaction-checkbox:checked');
    
    if (selectedTransactions.length === 0) {
        alert('Selecione pelo menos uma transação para transferir.');
        return;
    }
    
    // Carregar contas disponíveis
    loadAccountsForTransfer();
    updateSelectedCount();
    $('#transferModal').modal('show');
}

function transferSelectedTransactions() {
    showTransferModal();
}

function transferSingleTransaction(transactionId) {
    $('.transaction-checkbox').prop('checked', false);
    $(`.transaction-checkbox[value="${transactionId}"]`).prop('checked', true);
    showTransferModal();
}

function loadAccountsForTransfer() {
    $.get('{{ route("account-management.accounts-for-transfer") }}', {
        current_account_id: {{ $account->id }}
    })
    .done(function(accounts) {
        let options = '<option value="">Selecione a conta...</option>';
        accounts.forEach(function(account) {
            const typeIcons = {
                'checking': 'fa-university',
                'savings': 'fa-piggy-bank', 
                'credit_card': 'fa-credit-card',
                'investment': 'fa-chart-line'
            };
            
            options += `<option value="${account.id}">
                ${account.name} (${account.bank_name}) - ${account.type}
            </option>`;
        });
        
        $('#target_account_id').html(options);
    })
    .fail(function() {
        alert('Erro ao carregar contas disponíveis.');
    });
}

function executeTransfer() {
    const selectedTransactions = $('.transaction-checkbox:checked');
    const targetAccountId = $('#target_account_id').val();
    const notes = $('#transfer_notes').val();
    
    if (!targetAccountId) {
        alert('Selecione a conta de destino.');
        return;
    }
    
    if (selectedTransactions.length === 0) {
        alert('Selecione pelo menos uma transação.');
        return;
    }
    
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Transferindo...';
    
    // Processar cada transação
    let completed = 0;
    let errors = 0;
    
    selectedTransactions.each(function() {
        const transactionId = $(this).val();
        
        $.post('{{ route("account-management.transfer-transaction") }}', {
            transaction_id: transactionId,
            target_account_id: targetAccountId,
            notes: notes,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            completed++;
            checkTransferComplete();
        })
        .fail(function() {
            errors++;
            checkTransferComplete();
        });
    });
    
    function checkTransferComplete() {
        if (completed + errors === selectedTransactions.length) {
            button.disabled = false;
            button.innerHTML = 'Transferir';
            
            if (errors === 0) {
                alert(`${completed} transação(ões) transferida(s) com sucesso!`);
                location.reload();
            } else {
                alert(`${completed} transação(ões) transferida(s), ${errors} com erro.`);
                location.reload();
            }
            
            $('#transferModal').modal('hide');
        }
    }
}
</script>
@endpush
