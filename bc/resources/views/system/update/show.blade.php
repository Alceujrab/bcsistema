@extends('layouts.app')

@section('title', 'Detalhes da Atualização')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-info-circle text-primary me-2"></i>
            Detalhes da Atualização
        </h1>
        <p class="text-muted mb-0">Versão: <strong>{{ $update->version }}</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ route('system.update.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
        @if($update->canApply())
            <button class="btn btn-success" onclick="applyUpdate('{{ $update->id }}')">
                <i class="fas fa-download me-2"></i>
                Aplicar Atualização
            </button>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Informações da Atualização -->
        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações da Atualização
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nome:</strong>
                        <p class="text-muted mb-0">{{ $update->name ?? 'Sem nome' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Versão:</strong>
                        <p class="text-muted mb-0">
                            <span class="badge badge-primary">{{ $update->version }}</span>
                        </p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="text-muted mb-0">
                            @switch($update->status)
                                @case('available')
                                    <span class="badge badge-warning">Disponível</span>
                                    @break
                                @case('downloading')
                                    <span class="badge badge-info">Baixando</span>
                                    @break
                                @case('downloaded')
                                    <span class="badge badge-success">Baixado</span>
                                    @break
                                @case('applying')
                                    <span class="badge badge-primary">Aplicando</span>
                                    @break
                                @case('applied')
                                    <span class="badge badge-success">Aplicado</span>
                                    @break
                                @case('failed')
                                    <span class="badge badge-danger">Falhou</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($update->status) }}</span>
                            @endswitch
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Data de Criação:</strong>
                        <p class="text-muted mb-0">{{ $update->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                @if($update->applied_at)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Aplicado em:</strong>
                        <p class="text-muted mb-0">{{ $update->applied_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                @endif

                @if($update->file_size)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tamanho do Arquivo:</strong>
                        <p class="text-muted mb-0">
                            @php
                                $size = $update->file_size;
                                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
                                    $size /= 1024;
                                }
                                $formattedSize = round($size, 2) . ' ' . $units[$i];
                            @endphp
                            {{ $formattedSize }}
                        </p>
                    </div>
                </div>
                @endif

                @if($update->description)
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Descrição:</strong>
                        <p class="text-muted">{{ $update->description }}</p>
                    </div>
                </div>
                @endif

                @if($update->changelog)
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Changelog:</strong>
                        <div class="border p-3 bg-light">
                            {!! nl2br(e($update->changelog)) !!}
                        </div>
                    </div>
                </div>
                @endif

                @if($update->error_message)
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Mensagem de Erro:</strong>
                        <div class="alert alert-danger">
                            {{ $update->error_message }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Ações -->
        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-cogs me-2"></i>
                    Ações Disponíveis
                </h6>
            </div>
            <div class="card-body">
                @if($update->canApply())
                    <button class="btn btn-success btn-block mb-2" onclick="applyUpdate('{{ $update->id }}')">
                        <i class="fas fa-download me-2"></i>
                        Aplicar Atualização
                    </button>
                @endif

                <button class="btn btn-info btn-block mb-2" onclick="checkUpdateStatus('{{ $update->id }}')">
                    <i class="fas fa-sync me-2"></i>
                    Verificar Status
                </button>

                @if($update->status === 'failed')
                    <button class="btn btn-warning btn-block mb-2" onclick="retryUpdate('{{ $update->id }}')">
                        <i class="fas fa-redo me-2"></i>
                        Tentar Novamente
                    </button>
                @endif

                <a href="{{ route('system.update.index') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar à Lista
                </a>
            </div>
        </div>

        <!-- Informações Técnicas -->
        <div class="card shadow">
            <div class="card-header bg-gradient-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-server me-2"></i>
                    Informações Técnicas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>ID:</strong>
                    <span class="text-muted">{{ $update->id }}</span>
                </div>
                
                @if($update->download_url)
                <div class="mb-2">
                    <strong>URL de Download:</strong>
                    <small class="text-muted d-block">{{ Str::limit($update->download_url, 40) }}</small>
                </div>
                @endif

                @if($update->checksum)
                <div class="mb-2">
                    <strong>Checksum:</strong>
                    <small class="text-muted d-block font-monospace">{{ $update->checksum }}</small>
                </div>
                @endif

                <div class="mb-2">
                    <strong>Pode Aplicar:</strong>
                    <span class="badge badge-{{ $update->canApply() ? 'success' : 'secondary' }}">
                        {{ $update->canApply() ? 'Sim' : 'Não' }}
                    </span>
                </div>

                <div class="mb-2">
                    <strong>Já Aplicado:</strong>
                    <span class="badge badge-{{ $update->isApplied() ? 'success' : 'secondary' }}">
                        {{ $update->isApplied() ? 'Sim' : 'Não' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyUpdate(updateId) {
    if (confirm('Tem certeza que deseja aplicar esta atualização? Esta ação não pode ser desfeita.')) {
        // Aqui você pode adicionar a lógica para aplicar a atualização
        fetch(`/system/update/apply/${updateId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                create_backup: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Atualização iniciada com sucesso!');
                location.reload();
            } else {
                alert('Erro ao aplicar atualização: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao aplicar atualização');
        });
    }
}

function checkUpdateStatus(updateId) {
    fetch(`/system/update/status/${updateId}`)
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao verificar status');
        });
}

function retryUpdate(updateId) {
    if (confirm('Tem certeza que deseja tentar aplicar esta atualização novamente?')) {
        applyUpdate(updateId);
    }
}
</script>
@endpush
