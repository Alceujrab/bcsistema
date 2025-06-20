@extends('layouts.app')

@section('title', 'Sistema de Atualizações')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sync-alt text-primary me-2"></i>
            Sistema de Atualizações
        </h1>
        <p class="text-muted mb-0">Versão atual: <strong>{{ $systemInfo['current_version'] }}</strong></p>
    </div>
    <div class="btn-group">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-cloud-upload-alt me-2"></i>
            Upload de Atualização
        </button>
        <button class="btn btn-outline-info" onclick="createBackup()">
            <i class="fas fa-shield-alt me-2"></i>
            Criar Backup
        </button>
        <button class="btn btn-outline-secondary" onclick="refreshSystemInfo()">
            <i class="fas fa-info-circle me-2"></i>
            Info do Sistema
        </button>
    </div>
</div>
@endsection

@section('content')
<!-- Alertas de Status -->
<div id="alert-container"></div>

<!-- Informações da Versão Atual -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Versão Atual
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $systemInfo['current_version'] }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ config('app.name') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-code-branch fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $hasUpdates ? 'warning' : 'success' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $hasUpdates ? 'warning' : 'success' }} text-uppercase mb-1">
                            Atualizações
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ count($availableUpdates) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ $hasUpdates ? 'Disponíveis' : 'Sistema atualizado' }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-{{ $hasUpdates ? 'exclamation-triangle' : 'check-circle' }} fa-2x text-gray-300"></i>
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
                            Espaço em Disco
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($systemInfo['disk_space']['percentage_used'], 1) }}%
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ formatBytes($systemInfo['disk_space']['free']) }} livres
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hdd fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $systemInfo['environment'] === 'production' ? 'danger' : 'warning' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $systemInfo['environment'] === 'production' ? 'danger' : 'warning' }} text-uppercase mb-1">
                            Ambiente
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ ucfirst($systemInfo['environment']) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            PHP {{ $systemInfo['php_version'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($hasUpdates)
<!-- Atualizações Disponíveis -->
<div class="card shadow mb-4">
    <div class="card-header bg-gradient-warning text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-download me-2"></i>
            Atualizações Disponíveis ({{ count($availableUpdates) }})
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Versão</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Tamanho</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($availableUpdates as $update)
                    <tr>
                        <td>
                            <span class="badge badge-primary">{{ $update['version'] }}</span>
                            @if(version_compare($update['version'], $currentVersion) > 0)
                                <span class="badge badge-success ms-1">Nova</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $update['name'] ?? 'Sem nome' }}</strong>
                        </td>
                        <td>
                            {{ Str::limit($update['description'] ?? 'Sem descrição', 100) }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($update['created_at'])->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            {{ formatBytes($update['file_size']) }}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('system.update.show', $update['id']) }}" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-outline-success" 
                                        onclick="validateUpdate('{{ $update['id'] }}')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-outline-danger" 
                                        onclick="removeUpdate('{{ $update['id'] }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<!-- Nenhuma Atualização -->
<div class="card shadow mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
        <h4 class="text-gray-800">Sistema Atualizado</h4>
        <p class="text-muted mb-4">Não há atualizações disponíveis no momento.</p>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-2"></i>
            Enviar Nova Atualização
        </button>
    </div>
</div>
@endif

<!-- Informações do Sistema -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-gradient-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-server me-2"></i>
                    Informações do Sistema
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Sistema Operacional:</strong></td>
                        <td>{{ PHP_OS }}</td>
                    </tr>
                    <tr>
                        <td><strong>PHP:</strong></td>
                        <td>{{ $systemInfo['php_version'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Laravel:</strong></td>
                        <td>{{ $systemInfo['laravel_version'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ambiente:</strong></td>
                        <td>
                            <span class="badge badge-{{ $systemInfo['environment'] === 'production' ? 'danger' : 'warning' }}">
                                {{ ucfirst($systemInfo['environment']) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Debug:</strong></td>
                        <td>
                            <span class="badge badge-{{ $systemInfo['debug_mode'] ? 'warning' : 'success' }}">
                                {{ $systemInfo['debug_mode'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Banco de Dados:</strong></td>
                        <td>{{ strtoupper($systemInfo['database_driver']) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Memória:</strong></td>
                        <td>{{ $systemInfo['memory_limit'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempo Execução:</strong></td>
                        <td>{{ $systemInfo['max_execution_time'] }}s</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-gradient-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-shield-alt me-2"></i>
                    Backups Recentes
                </h6>
            </div>
            <div class="card-body">
                <div id="backups-list">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mt-2">Carregando backups...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cloud-upload-alt me-2"></i>
                    Upload de Atualização
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="update_file" class="form-label">Arquivo de Atualização (.zip)</label>
                        <input type="file" class="form-control" id="update_file" name="update_file" 
                               accept=".zip" required>
                        <div class="form-text">
                            Arquivo ZIP contendo a atualização com arquivo update.json
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Estrutura esperada:</strong>
                        <ul class="mb-0 mt-2">
                            <li>update.json (metadados)</li>
                            <li>files/ (arquivos do sistema)</li>
                            <li>migrations/ (migrações do BD)</li>
                            <li>scripts/ (scripts personalizados)</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="uploadUpdate()">
                    <i class="fas fa-upload me-2"></i>
                    Enviar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.progress-update {
    height: 25px;
    border-radius: 8px;
}

.update-step {
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    background: #f8f9fa;
    border-left: 4px solid #dee2e6;
}

.update-step.active {
    background: #e3f2fd;
    border-left-color: #2196f3;
}

.update-step.completed {
    background: #e8f5e8;
    border-left-color: #4caf50;
}

.update-step.error {
    background: #ffebee;
    border-left-color: #f44336;
}
</style>
@endpush

@push('scripts')
<script>
// Carregar backups ao inicializar
document.addEventListener('DOMContentLoaded', function() {
    loadBackups();
});

function loadBackups() {
    fetch('{{ route("system.update.backups") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBackups(data.backups);
            }
        })
        .catch(error => {
            document.getElementById('backups-list').innerHTML = 
                '<div class="alert alert-danger">Erro ao carregar backups</div>';
        });
}

function displayBackups(backups) {
    const container = document.getElementById('backups-list');
    
    if (backups.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Nenhum backup encontrado</p>';
        return;
    }
    
    let html = '';
    backups.slice(0, 5).forEach(backup => {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${backup.name}</strong><br>
                    <small class="text-muted">${formatBytes(backup.size)} - ${backup.created_at}</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="downloadBackup('${backup.name}')">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="restoreBackup('${backup.name}')">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function uploadUpdate() {
    const formData = new FormData(document.getElementById('uploadForm'));
    
    showAlert('info', 'Enviando arquivo de atualização...');
    
    fetch('{{ route("system.update.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro ao enviar arquivo: ' + error.message);
    });
    
    document.querySelector('#uploadModal .btn-close').click();
}

function validateUpdate(updateId) {
    showAlert('info', 'Validando atualização...');
    
    fetch(`{{ route('system.update.validate', '') }}/${updateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const validation = data.validation;
                
                if (validation.valid) {
                    if (confirm('Atualização válida! Deseja aplicar agora?')) {
                        applyUpdate(updateId);
                    } else {
                        showAlert('info', 'Atualização validada com sucesso');
                    }
                } else {
                    let errorMsg = 'Validação falhou:\n' + validation.errors.join('\n');
                    showAlert('danger', errorMsg);
                }
            } else {
                showAlert('danger', data.error);
            }
        })
        .catch(error => {
            showAlert('danger', 'Erro na validação: ' + error.message);
        });
}

function applyUpdate(updateId) {
    if (!confirm('ATENÇÃO: Esta operação irá atualizar o sistema. Um backup será criado automaticamente. Continuar?')) {
        return;
    }
    
    showAlert('warning', 'Aplicando atualização... Por favor, aguarde e não feche esta página.');
    
    const progressHtml = `
        <div class="progress progress-update mb-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                 style="width: 0%"></div>
        </div>
        <div id="update-steps"></div>
    `;
    
    document.getElementById('alert-container').innerHTML = 
        '<div class="alert alert-warning">' + progressHtml + '</div>';
    
    // Simular progresso
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress >= 90) progress = 90;
        
        document.querySelector('.progress-bar').style.width = progress + '%';
    }, 500);
    
    fetch(`{{ route('system.update.apply', '') }}/${updateId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ confirmed: true })
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        document.querySelector('.progress-bar').style.width = '100%';
        
        if (data.success) {
            showAlert('success', `${data.message}<br><strong>Nova versão:</strong> ${data.version}`);
            setTimeout(() => location.reload(), 3000);
        } else {
            showAlert('danger', data.error);
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        showAlert('danger', 'Erro ao aplicar atualização: ' + error.message);
    });
}

function createBackup() {
    if (!confirm('Criar backup do sistema atual?')) return;
    
    showAlert('info', 'Criando backup...');
    
    fetch('{{ route("system.update.backup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadBackups();
        } else {
            showAlert('danger', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro ao criar backup: ' + error.message);
    });
}

function removeUpdate(updateId) {
    if (!confirm('Remover este arquivo de atualização?')) return;
    
    fetch(`{{ route('system.update.remove', '') }}/${updateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('danger', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro ao remover atualização: ' + error.message);
    });
}

function downloadBackup(backupName) {
    window.location.href = `{{ route('system.update.download-backup', '') }}/${backupName}`;
}

function restoreBackup(backupName) {
    if (!confirm('ATENÇÃO: Esta operação irá restaurar o sistema para o estado do backup. Todos os dados atuais serão perdidos. Continuar?')) {
        return;
    }
    
    showAlert('warning', 'Restaurando backup...');
    
    fetch(`{{ route('system.update.restore-backup', '') }}/${backupName}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ confirmed: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('danger', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro ao restaurar backup: ' + error.message);
    });
}

function refreshSystemInfo() {
    fetch('{{ route("system.update.system-info") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Informações do sistema atualizadas');
                // Atualizar informações na tela se necessário
            } else {
                showAlert('danger', 'Erro ao buscar informações do sistema');
            }
        });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.getElementById('alert-container').innerHTML = alertHtml;
    
    // Auto-dismiss após 5 segundos para alertas de sucesso/info
    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
