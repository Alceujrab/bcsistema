@extends('layouts.app')

@section('title', 'Importação #' . $import->id)

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-file-import text-primary me-2"></i>
            Importação #{{ $import->id }}
        </h1>
        <p class="text-muted mb-0">
            {{ $import->filename }} • 
            {{ $import->created_at->format('d/m/Y H:i:s') }} • 
            <span class="badge bg-{{ $import->status_color }}">{{ ucfirst($import->status) }}</span>
        </p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('imports.index') }}">Importações</a></li>
            <li class="breadcrumb-item active">#{{ $import->id }}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<!-- Cards de Status -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-list-ol fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $import->total_transactions }}</h3>
                <small>Total de Transações</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $import->imported_transactions }}</h3>
                <small>Importadas</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $import->duplicate_transactions }}</h3>
                <small>Duplicadas</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $import->error_transactions }}</h3>
                <small>Erros</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-xl-8 col-lg-7">
        <!-- Resumo da Importação -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Resumo da Importação
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-file text-primary me-2"></i>
                                Arquivo
                            </label>
                            <div class="info-value">
                                <span class="badge bg-light text-dark">{{ $import->filename }}</span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-file-code text-info me-2"></i>
                                Tipo
                            </label>
                            <div class="info-value">
                                <span class="badge bg-info">{{ strtoupper($import->file_type) }}</span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-university text-success me-2"></i>
                                Conta Bancária
                            </label>
                            <div class="info-value">
                                {{ $import->bankAccount->name }} - {{ $import->bankAccount->bank_name }}
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-user text-secondary me-2"></i>
                                Importado por
                            </label>
                            <div class="info-value">
                                {{ $import->importer->name }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status e Métricas -->
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-flag text-warning me-2"></i>
                                Status
                            </label>
                            <div class="info-value">
                                <span class="badge bg-{{ $import->status_color }} fs-6">
                                    <i class="fas fa-{{ $import->status == 'completed' ? 'check' : ($import->status == 'processing' ? 'spinner fa-spin' : 'times') }} me-1"></i>
                                    {{ ucfirst($import->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-calendar text-info me-2"></i>
                                Data da Importação
                            </label>
                            <div class="info-value">
                                {{ $import->created_at->format('d/m/Y') }} às {{ $import->created_at->format('H:i:s') }}
                                <small class="text-muted d-block">{{ $import->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        
                        @if($import->processed_at)
                        <div class="info-item mb-3">
                            <label class="info-label">
                                <i class="fas fa-clock text-success me-2"></i>
                                Processamento
                            </label>
                            <div class="info-value">
                                {{ $import->processed_at->format('d/m/Y H:i:s') }}
                                <small class="text-muted d-block">
                                    Duração: {{ $import->created_at->diffInSeconds($import->processed_at) }}s
                                </small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Barra de Progresso -->
                @if($import->success_rate > 0)
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="info-label mb-0">Taxa de Sucesso</label>
                        <span class="fw-bold text-{{ $import->success_rate >= 90 ? 'success' : ($import->success_rate >= 70 ? 'warning' : 'danger') }}">
                            {{ $import->success_rate }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-{{ $import->success_rate >= 90 ? 'success' : ($import->success_rate >= 70 ? 'warning' : 'danger') }}" 
                             role="progressbar" style="width: {{ $import->success_rate }}%">
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Log de Importação -->
        @if($import->import_log && count($import->import_log) > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt text-secondary me-2"></i>
                        Log de Importação
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportLog()">
                        <i class="fas fa-download me-1"></i>
                        Exportar Log
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="log-container" style="max-height: 400px; overflow-y: auto;">
                    @foreach($import->import_log as $index => $log)
                    <div class="log-entry {{ $index % 2 == 0 ? 'bg-light' : '' }} p-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <span class="badge bg-secondary me-3 mt-1">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <code class="text-dark">{{ $log }}</code>
                            </div>
                            @if(str_contains(strtolower($log), 'erro'))
                                <i class="fas fa-exclamation-triangle text-danger ms-2"></i>
                            @elseif(str_contains(strtolower($log), 'sucesso'))
                                <i class="fas fa-check-circle text-success ms-2"></i>
                            @elseif(str_contains(strtolower($log), 'duplicad'))
                                <i class="fas fa-copy text-warning ms-2"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Transações Importadas -->
        @if($transactions->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Transações Importadas ({{ $transactions->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $transaction->transaction_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ Str::limit($transaction->description, 50) }}</div>
                                            @if($transaction->reference_number)
                                            <small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} R$ {{ number_format(abs($transaction->amount), 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                        {{ $transaction->type === 'credit' ? 'Crédito' : 'Débito' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'reconciled' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pendente',
                                            'reconciled' => 'Conciliado',
                                            'cancelled' => 'Cancelado'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$transaction->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$transaction->status] ?? 'Indefinido' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Ver detalhes da transação">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Mostrando {{ $transactions->firstItem() }} a {{ $transactions->lastItem() }} de {{ $transactions->total() }} transações
                        </small>
                        {{ $transactions->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                <p class="text-muted">Esta importação não gerou transações ou elas ainda estão sendo processadas.</p>
                @if($import->status === 'processing')
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Aguarde enquanto processamos sua importação...</p>
                @endif
            </div>
        </div>
        @endif
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-xl-4 col-lg-5">
        <!-- Ações Rápidas -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('imports.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar para Lista
                    </a>
                    <a href="{{ route('imports.create', ['bank_account_id' => $import->bank_account_id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-file-import me-2"></i>
                        Nova Importação
                    </a>
                    <a href="{{ route('transactions.index', ['bank_account_id' => $import->bank_account_id]) }}" 
                       class="btn btn-info">
                        <i class="fas fa-list me-2"></i>
                        Ver Transações
                    </a>
                    <a href="{{ route('bank-accounts.show', $import->bank_account_id) }}" 
                       class="btn btn-success">
                        <i class="fas fa-university me-2"></i>
                        Detalhes da Conta
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas Detalhadas -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie text-info me-2"></i>
                    Estatísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-file-upload text-primary"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label">Tamanho do Arquivo</div>
                            <div class="stat-value">
                                @php
                                    $filePath = storage_path('app/imports/' . $import->filename);
                                    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                @endphp
                                {{ $fileSize > 0 ? number_format($fileSize / 1024, 2) . ' KB' : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label">Tempo de Processamento</div>
                            <div class="stat-value">
                                @if($import->processed_at)
                                    {{ $import->created_at->diffInSeconds($import->processed_at) }}s
                                @else
                                    Em processamento...
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-percentage text-success"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label">Taxa de Sucesso</div>
                            <div class="stat-value">{{ $import->success_rate }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Perigosas -->
        @if($import->status == 'completed' && $import->imported_transactions > 0)
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Zona de Perigo
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <div class="d-flex">
                        <i class="fas fa-shield-alt fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="alert-heading">Atenção!</h6>
                            <p class="mb-2 small">Excluir esta importação removerá permanentemente:</p>
                            <ul class="mb-0 small">
                                <li>{{ $import->imported_transactions }} transações importadas</li>
                                <li>Registros de log e histórico</li>
                                <li>Referências desta importação</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-2"></i>
                    Excluir Importação
                </button>
            </div>
        </div>
        @endif

        <!-- Ajuda -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle text-info me-2"></i>
                    Precisa de Ajuda?
                </h5>
            </div>
            <div class="card-body">
                <div class="help-links">
                    <a href="#" class="help-link" data-bs-toggle="tooltip" title="Como interpretar os logs de importação">
                        <i class="fas fa-book me-2"></i>
                        Interpretar Logs
                    </a>
                    <a href="#" class="help-link" data-bs-toggle="tooltip" title="Como resolver erros comuns">
                        <i class="fas fa-wrench me-2"></i>
                        Resolver Erros
                    </a>
                    <a href="#" class="help-link" data-bs-toggle="tooltip" title="Formatos de arquivo suportados">
                        <i class="fas fa-file-alt me-2"></i>
                        Formatos Suportados
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                    <h5>Tem certeza que deseja excluir esta importação?</h5>
                    <p class="text-muted">Esta ação não pode ser desfeita e removerá:</p>
                </div>
                
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <li><strong>{{ $import->imported_transactions }}</strong> transações importadas</li>
                        <li>Todo o histórico e logs</li>
                        <li>Referências desta importação</li>
                    </ul>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                    <label class="form-check-label" for="confirmDelete">
                        Entendo que esta ação é permanente e irreversível
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
                <form action="{{ route('imports.destroy', $import) }}" method="POST" class="d-inline" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="fas fa-trash me-2"></i>
                        Excluir Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    border-left: 4px solid var(--bs-primary);
    padding-left: 15px;
}

.info-label {
    font-weight: 600;
    color: var(--bs-secondary);
    font-size: 14px;
    margin-bottom: 5px;
    display: block;
}

.info-value {
    font-size: 15px;
    color: var(--bs-dark);
}

.log-container {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

.log-entry:hover {
    background-color: var(--bs-gray-100) !important;
}

.stats-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 12px;
    background-color: var(--bs-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--bs-primary);
}

.stat-icon {
    margin-right: 15px;
    font-size: 18px;
}

.stat-label {
    font-size: 12px;
    color: var(--bs-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: var(--bs-dark);
}

.help-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.help-link {
    padding: 8px 12px;
    border-radius: 6px;
    color: var(--bs-dark);
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid var(--bs-gray-200);
}

.help-link:hover {
    background-color: var(--bs-primary);
    color: white;
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .stat-item {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox de confirmação
    const confirmCheckbox = document.getElementById('confirmDelete');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmCheckbox && confirmBtn) {
        confirmCheckbox.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
        });
    }

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-refresh para importações em processamento
    @if($import->status == 'processing')
    setTimeout(function() {
        location.reload();
    }, 10000); // Recarrega a cada 10 segundos
    @endif
});

// Função para exportar log
function exportLog() {
    const logs = @json($import->import_log ?? []);
    const filename = 'importacao-{{ $import->id }}-log.txt';
    
    let content = 'LOG DE IMPORTAÇÃO #{{ $import->id }}\n';
    content += '=================================\n';
    content += 'Arquivo: {{ $import->filename }}\n';
    content += 'Data: {{ $import->created_at->format("d/m/Y H:i:s") }}\n';
    content += 'Status: {{ $import->status }}\n';
    content += '=================================\n\n';
    
    logs.forEach((log, index) => {
        content += `${index + 1}. ${log}\n`;
    });
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('imports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar para Lista
                    </a>
                    <a href="{{ route('imports.create', ['bank_account_id' => $import->bank_account_id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-file-import me-2"></i> Nova Importação
                    </a>
                    <a href="{{ route('transactions.index', ['bank_account_id' => $import->bank_account_id]) }}" 
                       class="btn btn-info">
                        <i class="fas fa-list me-2"></i> Ver Transações
                    </a>
                    
                    @if($import->status == 'completed' && $import->imported_transactions > 0)
                    <hr class="my-3">
                    <form action="{{ route('imports.destroy', $import) }}" method="POST" 
                          onsubmit="return confirm('ATENÇÃO: Isso excluirá permanentemente esta importação e todas as transações não conciliadas associadas. Deseja continuar?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i> Excluir Importação
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">
                        Esta ação removerá todas as transações não conciliadas desta importação.
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection