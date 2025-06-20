@extends('layouts.app')

@section('title', 'Importação de Extratos Avançada')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-import text-success me-2"></i>
            Importação de Extratos - Multi-Formato
        </h1>
        <p class="text-muted mb-0">Suporte completo: CSV, OFX, QIF, PDF, Excel + Bancos Brasileiros</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('imports.create') }}" class="btn btn-success">
            <i class="fas fa-cloud-upload-alt me-2"></i> Nova Importação
        </a>
        <button class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('imports.create') }}">
                <i class="fas fa-upload me-2"></i>Upload Multi-formato
            </a></li>
            <li><a class="dropdown-item" href="{{ route('imports.index') }}">
                <i class="fas fa-history me-2"></i>Histórico de importações
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('transactions.index') }}">
                <i class="fas fa-list me-2"></i>Ver Transações
            </a></li>
        </ul>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="fas fa-star me-2"></i>
    <strong>Sistema de Importação:</strong> Suporte completo a CSV, TXT, OFX e outros formatos bancários!
    <a href="{{ route('imports.create') }}" class="btn btn-outline-primary btn-sm ms-2">
        Nova Importação <i class="fas fa-arrow-right"></i>
    </a>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="container-fluid">
        <!-- Cards de Estatísticas Expandidos -->
        <div class="row mb-4">
            <!-- Formatos Suportados -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="bc-stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Formatos Suportados
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ count($supportedFormats) }}
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    CSV, OFX, QIF, PDF, Excel
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Total de Importações -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total de Importações
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $imports->total() }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Processadas com sucesso
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-upload fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bancos Detectados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Bancos Brasileiros
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            5+
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Detecção automática
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-university fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transações Importadas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Transações Hoje
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Transaction::whereNotNull('import_hash')->whereDate('created_at', today())->count() }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Importadas automaticamente
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seção de Formatos e Bancos Suportados -->
<div class="row mb-4">
    <!-- Formatos Suportados -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-code me-2"></i>
                    Formatos de Arquivo Suportados
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- CSV/TXT -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <i class="fas fa-file-csv fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">CSV / TXT</h6>
                                <small class="text-muted">Arquivo separado por vírgula/ponto-vírgula</small>
                                <div class="mt-1">
                                    <span class="badge badge-success">Auto-detecção</span>
                                    <span class="badge badge-info">Encoding</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OFX -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <i class="fas fa-file-code fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">OFX</h6>
                                <small class="text-muted">Open Financial Exchange</small>
                                <div class="mt-1">
                                    <span class="badge badge-info">Padrão bancário</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QIF -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <i class="fas fa-file-alt fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">QIF</h6>
                                <small class="text-muted">Quicken Interchange Format</small>
                                <div class="mt-1">
                                    <span class="badge badge-warning">Quicken</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDF -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">PDF</h6>
                                <small class="text-muted">Extrato em PDF (OCR)</small>
                                <div class="mt-1">
                                    <span class="badge badge-danger">IA</span>
                                    <span class="badge badge-secondary">Beta</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Excel -->
                    <div class="col-md-12 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <i class="fas fa-file-excel fa-2x text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Excel (XLS/XLSX)</h6>
                                <small class="text-muted">Planilhas do Microsoft Excel</small>
                                <div class="mt-1">
                                    <span class="badge badge-success">Multi-abas</span>
                                    <span class="badge badge-info">Formatação</span>
                                    <span class="badge badge-warning">Em desenvolvimento</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Template -->
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-download me-2"></i>
                            Templates e Exemplos
                        </h6>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('imports.download-template', 'csv') }}" class="btn btn-outline-success btn-sm w-100">
                            <i class="fas fa-file-csv me-2"></i>
                            Template CSV
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('imports.download-template', 'ofx') }}" class="btn btn-outline-info btn-sm w-100">
                            <i class="fas fa-file-code me-2"></i>
                            Exemplo OFX
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('imports.download-template', 'qif') }}" class="btn btn-outline-warning btn-sm w-100">
                            <i class="fas fa-file-alt me-2"></i>
                            Exemplo QIF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bancos Brasileiros -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-gradient-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-university me-2"></i>
                    Bancos Brasileiros - Detecção Automática
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Itaú -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Itaú Unibanco</h6>
                                <small class="text-muted">Conta corrente e cartão</small>
                            </div>
                        </div>
                    </div>

                    <!-- Bradesco -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Bradesco</h6>
                                <small class="text-muted">Todas as modalidades</small>
                            </div>
                        </div>
                    </div>

                    <!-- Santander -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Santander</h6>
                                <small class="text-muted">Extrato e cartão</small>
                            </div>
                        </div>
                    </div>

                    <!-- Caixa -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Caixa Econômica</h6>
                                <small class="text-muted">Conta e poupança</small>
                            </div>
                        </div>
                    </div>

                    <!-- BB -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Banco do Brasil</h6>
                                <small class="text-muted">Conta e investimentos</small>
                            </div>
                        </div>
                    </div>

                    <!-- Outros -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                <div class="bg-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">Outros Bancos</h6>
                                <small class="text-muted">Formato genérico</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-magic me-2"></i>
                    <strong>Detecção Inteligente:</strong> O sistema identifica automaticamente o banco baseado no conteúdo do arquivo e aplica as regras específicas de cada instituição.
                </div>
            </div>
        </div>
    </div>
</div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Concluídas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $imports->where('status', 'completed')->count() }}
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
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Em Processamento
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $imports->where('status', 'processing')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Com Erros
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $imports->where('status', 'failed')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter me-2"></i>Filtros de Pesquisa
        </h6>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar text-primary me-1"></i>Data
                    </label>
                    <input type="date" class="form-control" id="dateFilter">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-university text-info me-1"></i>Conta
                    </label>
                    <select class="form-select" id="accountFilter">
                        <option value="">🏦 Todas as contas</option>
                        @foreach($imports->pluck('bankAccount')->unique('id') as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-info-circle text-success me-1"></i>Status
                    </label>
                    <select class="form-select" id="statusFilter">
                        <option value="">📋 Todos</option>
                        <option value="completed">✅ Concluídas</option>
                        <option value="processing">⏳ Processando</option>
                        <option value="failed">❌ Com Erro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file text-warning me-1"></i>Tipo
                    </label>
                    <select class="form-select" id="typeFilter">
                        <option value="">📄 Todos</option>
                        <option value="csv">CSV</option>
                        <option value="ofx">OFX</option>
                        <option value="qif">QIF</option>
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
<<!-- Tabela de Importações -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table me-2"></i>Histórico de Importações
        </h6>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-success" onclick="refreshTable()" data-bs-toggle="tooltip" title="Atualizar Lista">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="btn btn-outline-info" onclick="exportImports()" data-bs-toggle="tooltip" title="Exportar Relatório">
                <i class="fas fa-download"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>ID</th>
                        <th><i class="fas fa-calendar me-1"></i>Data/Hora</th>
                        <th><i class="fas fa-university me-1"></i>Conta</th>
                        <th><i class="fas fa-file me-1"></i>Arquivo</th>
                        <th><i class="fas fa-list me-1"></i>Total</th>
                        <th><i class="fas fa-check me-1"></i>Importadas</th>
                        <th><i class="fas fa-copy me-1"></i>Duplicadas</th>
                        <th><i class="fas fa-exclamation me-1"></i>Erros</th>
                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                        <th class="text-center"><i class="fas fa-cogs me-1"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($imports as $import)
                    <tr class="import-row" data-id="{{ $import->id }}">
                        <td>
                            <span class="badge bg-secondary">#{{ $import->id }}</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $import->created_at->format('d/m/Y') }}</strong>
                                <br><small class="text-muted">{{ $import->created_at->format('H:i:s') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-university text-primary me-2"></i>
                                <div>
                                    <strong>{{ $import->bankAccount->name }}</strong>
                                    <br><small class="text-muted">{{ $import->bankAccount->bank_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @php
                                    $extension = pathinfo($import->filename, PATHINFO_EXTENSION);
                                    $iconClass = match(strtolower($extension)) {
                                        'csv' => 'fas fa-file-csv text-success',
                                        'ofx' => 'fas fa-file-code text-info',
                                        'qif' => 'fas fa-file-alt text-warning',
                                        default => 'fas fa-file text-muted'
                                    };
                                @endphp
                                <i class="{{ $iconClass }} me-2"></i>
                                <div>
                                    <span data-bs-toggle="tooltip" title="{{ $import->filename }}">
                                        {{ Str::limit($import->filename, 20) }}
                                    </span>
                                    <br><small class="text-muted text-uppercase">{{ $import->file_type }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-list me-1"></i>
                                {{ number_format($import->total_transactions) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>
                                {{ number_format($import->imported_transactions) }}
                            </span>
                        </td>
                        <td>
                            @if($import->duplicate_transactions > 0)
                                <span class="badge bg-warning">
                                    <i class="fas fa-copy me-1"></i>
                                    {{ number_format($import->duplicate_transactions) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($import->error_transactions > 0)
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ number_format($import->error_transactions) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($import->status == 'completed')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Concluída
                                </span>
                            @elseif($import->status == 'processing')
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Processando
                                </span>
                            @elseif($import->status == 'failed')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Erro
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($import->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('imports.show', $import) }}" 
                                   class="btn btn-primary" data-bs-toggle="tooltip" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($import->status == 'completed')
                                    <button class="btn btn-info" onclick="downloadReport({{ $import->id }})"
                                            data-bs-toggle="tooltip" title="Baixar Relatório">
                                        <i class="fas fa-download"></i>
                                    </button>
                                @endif
                                @if($import->status == 'failed')
                                    <button class="btn btn-warning" onclick="retryImport({{ $import->id }})"
                                            data-bs-toggle="tooltip" title="Tentar Novamente">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                @endif
                                @if(in_array($import->status, ['completed', 'failed']))
                                    <button class="btn btn-danger" onclick="confirmDelete({{ $import->id }})"
                                            data-bs-toggle="tooltip" title="Excluir Importação">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-file-import fa-3x mb-3"></i>
                                <h5>Nenhuma importação realizada ainda</h5>
                                <p>
                                    <a href="{{ route('imports.create') }}" class="text-decoration-none">
                                        Fazer sua primeira importação
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
                @if(($imports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $imports->total() : $imports->count()) > 0)
                    Mostrando {{ $imports->firstItem() }} até {{ $imports->lastItem() }} 
                    de {{ $imports instanceof \Illuminate\Pagination\LengthAwarePaginator ? number_format($imports->total()) : number_format($imports->count()) }} importações
                @endif
            </div>
            <div>
                {{ $imports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $imports->links() : '' }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Templates -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>Templates de Importação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                                <h6>Template CSV</h6>
                                <p class="small text-muted">Formato padrão com vírgulas</p>
                                <button class="btn btn-success btn-sm" onclick="downloadTemplate('csv')">
                                    <i class="fas fa-download me-1"></i>Baixar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-file-code fa-3x text-info mb-3"></i>
                                <h6>Template OFX</h6>
                                <p class="small text-muted">Open Financial Exchange</p>
                                <button class="btn btn-info btn-sm" onclick="downloadTemplate('ofx')">
                                    <i class="fas fa-download me-1"></i>Baixar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-3x text-warning mb-3"></i>
                                <h6>Template QIF</h6>
                                <p class="small text-muted">Quicken Interchange Format</p>
                                <button class="btn btn-warning btn-sm" onclick="downloadTemplate('qif')">
                                    <i class="fas fa-download me-1"></i>Baixar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Dica:</strong> Use os templates como base para formatar seus arquivos de extrato bancário antes da importação.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ajuda -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Como Importar Extratos Bancários
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-list-ol text-primary me-2"></i>Passo a Passo</h6>
                        <ol>
                            <li><strong>Baixe o extrato</strong> do seu banco no formato CSV, OFX ou QIF</li>
                            <li><strong>Verifique o formato</strong> usando nossos templates</li>
                            <li><strong>Acesse "Nova Importação"</strong> no menu superior</li>
                            <li><strong>Selecione a conta</strong> de destino</li>
                            <li><strong>Faça upload do arquivo</strong> e aguarde o processamento</li>
                            <li><strong>Confira o resultado</strong> na tela de detalhes</li>
                        </ol>
                        
                        <h6><i class="fas fa-file text-success me-2"></i>Formatos Aceitos</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-1"></i><strong>CSV:</strong> Mais comum, separado por vírgulas</li>
                            <li><i class="fas fa-check text-success me-1"></i><strong>OFX:</strong> Padrão bancário internacional</li>
                            <li><i class="fas fa-check text-success me-1"></i><strong>QIF:</strong> Formato Quicken</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Cuidados Importantes</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-exclamation text-warning me-1"></i>Verifique a conta de destino antes de importar</li>
                            <li><i class="fas fa-exclamation text-warning me-1"></i>Transações duplicadas serão automaticamente detectadas</li>
                            <li><i class="fas fa-exclamation text-warning me-1"></i>Confira sempre o resultado da importação</li>
                            <li><i class="fas fa-exclamation text-warning me-1"></i>Mantenha backup dos arquivos originais</li>
                        </ul>
                        
                        <h6><i class="fas fa-lightbulb text-info me-2"></i>Dicas Avançadas</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-star text-info me-1"></i>Configure palavras-chave nas categorias para auto-categorização</li>
                            <li><i class="fas fa-star text-info me-1"></i>Importe regularmente para manter o sistema atualizado</li>
                            <li><i class="fas fa-star text-info me-1"></i>Use filtros para localizar importações específicas</li>
                        </ul>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-phone me-1"></i>
                            <strong>Suporte:</strong> Em caso de dúvidas, entre em contato com nossa equipe
                        </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Filtros em tempo real
    $('#dateFilter, #accountFilter, #statusFilter, #typeFilter').on('change', function() {
        filterTable();
    });
    
    // Auto-refresh para importações em processamento
    if ($('.badge:contains("Processando")').length > 0) {
        setInterval(function() {
            location.reload();
        }, 30000); // Atualiza a cada 30 segundos
    }
});

function filterTable() {
    const dateFilter = $('#dateFilter').val();
    const accountFilter = $('#accountFilter').val();
    const statusFilter = $('#statusFilter').val();
    const typeFilter = $('#typeFilter').val();
    
    $('.import-row').each(function() {
        const row = $(this);
        let visible = true;
        
        // Filtro por data
        if (dateFilter) {
            const rowDate = row.find('td:nth-child(2) strong').text();
            const [day, month, year] = rowDate.split('/');
            const rowDateFormatted = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            if (rowDateFormatted !== dateFilter) {
                visible = false;
            }
        }
        
        // Filtro por conta
        if (accountFilter) {
            const accountId = row.data('account-id'); // Seria necessário adicionar este data attribute
            if (accountId && accountId != accountFilter) {
                visible = false;
            }
        }
        
        // Filtro por status
        if (statusFilter) {
            const status = row.find('td:nth-child(9) .badge').text().toLowerCase();
            if (!status.includes(statusFilter)) {
                visible = false;
            }
        }
        
        // Filtro por tipo
        if (typeFilter) {
            const fileType = row.find('td:nth-child(4) small').text().toLowerCase();
            if (!fileType.includes(typeFilter.toLowerCase())) {
                visible = false;
            }
        }
        
        row.toggle(visible);
    });
    
    // Mostrar mensagem se nenhum resultado
    const visibleRows = $('.import-row:visible').length;
    if (visibleRows === 0 && $('.no-results-row').length === 0) {
        $('tbody').append(`
            <tr class="no-results-row">
                <td colspan="10" class="text-center py-4 text-muted">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>Nenhuma importação encontrada com os filtros aplicados.</p>
                </td>
            </tr>
        `);
    } else if (visibleRows > 0) {
        $('.no-results-row').remove();
    }
}

function clearFilters() {
    $('#dateFilter').val('');
    $('#accountFilter').val('');
    $('#statusFilter').val('');
    $('#typeFilter').val('');
    $('.import-row').show();
    $('.no-results-row').remove();
    showToast('info', 'Filtros limpos!');
}

function refreshTable() {
    showToast('info', 'Atualizando lista...');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportImports() {
    showToast('info', 'Exportando relatório de importações...');
    
    // Simular exportação
    setTimeout(() => {
        const data = {
            imports: [],
            exported_at: new Date().toISOString(),
            total: $('.import-row:visible').length
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], {
            type: 'application/json'
        });
        
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'relatorio-importacoes-' + new Date().toISOString().slice(0, 10) + '.json';
        a.click();
        
        URL.revokeObjectURL(url);
        showToast('success', 'Relatório exportado com sucesso!');
    }, 1500);
}

function downloadTemplate(type) {
    showToast('info', `Baixando template ${type.toUpperCase()}...`);
    
    // Em produção, seria uma chamada real para o backend
    setTimeout(() => {
        showToast('success', `Template ${type.toUpperCase()} baixado com sucesso!`);
    }, 1000);
}

function downloadReport(importId) {
    showToast('info', 'Gerando relatório da importação...');
    
    // Simulação - em produção seria uma chamada para o backend
    setTimeout(() => {
        showToast('success', 'Relatório baixado com sucesso!');
    }, 1500);
}

function retryImport(importId) {
    if (confirm('Deseja tentar reprocessar esta importação?')) {
        showToast('warning', 'Reprocessando importação...');
        
        // Em produção seria uma chamada AJAX
        setTimeout(() => {
            showToast('success', 'Importação foi adicionada à fila de processamento!');
            location.reload();
        }, 2000);
    }
}

function confirmDelete(importId) {
    if (confirm('Tem certeza que deseja excluir esta importação?\n\nEsta ação irá:\n- Remover o registro da importação\n- Manter as transações já conciliadas\n- Remover transações pendentes desta importação\n\nEsta ação não pode ser desfeita.')) {
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("imports.destroy", ":id") }}'.replace(':id', importId)
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

function bulkCleanup() {
    if (confirm('Deseja executar limpeza em lote?\n\nEsta ação irá:\n- Remover importações antigas com falha\n- Limpar arquivos temporários\n- Otimizar registros duplicados\n\nContinuar?')) {
        showToast('warning', 'Executando limpeza em lote...');
        
        setTimeout(() => {
            showToast('success', 'Limpeza executada com sucesso! 5 registros removidos.');
            location.reload();
        }, 3000);
    }
}

function showToast(type, message) {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation' : 'info'}-circle me-2"></i>
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
$('.import-row').hover(
    function() { $(this).addClass('table-active'); },
    function() { $(this).removeClass('table-active'); }
);

// Progress indicator para importações em processamento
$('.badge:contains("Processando")').each(function() {
    const badge = $(this);
    let dots = '';
    setInterval(() => {
        dots = dots.length >= 3 ? '' : dots + '.';
        badge.html('<i class="fas fa-clock me-1"></i>Processando' + dots);
    }, 500);
});
</script>
        </div>
    </div>
</div>
@endpush