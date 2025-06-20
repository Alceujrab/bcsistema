@extends('layouts.app')

@section('title', 'Nova Importação de Extrato')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/imports.css') }}">
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cloud-upload-alt text-success me-2"></i>
            Nova Importação de Extrato
        </h1>
        <p class="text-muted mb-0">Suporte completo para CSV, OFX, QIF, PDF e Excel</p>
    </div>
    <div>
        <a href="{{ route('extract-imports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="container-fluid">
        <!-- Barra de Progresso -->
        <div class="progress mb-4" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 33%">
                <span class="sr-only">1 de 3 etapas concluídas</span>
            </div>
        </div>

        <div class="row">
            <!-- Formulário Principal -->
            <div class="col-xl-8 col-lg-7">
                <div class="bc-section">
                    <h2 class="bc-title">
                        <i class="fas fa-upload me-2"></i>
                        Dados da Importação
                    </h2>
                    <div class="bc-card">
                        <div class="card-body">
                            <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <!-- Seleção de Conta -->
                    <div class="mb-4">
                        <label for="bank_account_id" class="form-label fw-bold">
                            <i class="fas fa-university text-primary me-2"></i>
                            Conta Bancária *
                        </label>
                        <select name="bank_account_id" id="bank_account_id" 
                                class="bc-form-control form-select form-select-lg @error('bank_account_id') is-invalid @enderror" 
                                required>
                            <option value="">🏦 Selecione uma conta bancária</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" 
                                        {{ old('bank_account_id', request('bank_account_id')) == $account->id ? 'selected' : '' }}
                                        data-bank="{{ $account->bank_name }}">
                                    {{ $account->name }} - {{ $account->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            Selecione a conta para a qual deseja importar as transações
                        </div>
                    </div>
                    
                    <!-- Tipo de Arquivo -->
                    <div class="mb-4">
                        <label for="file_type" class="form-label fw-bold">
                            <i class="fas fa-file-alt text-info me-2"></i>
                            Tipo de Arquivo *
                        </label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="file_type" id="csv" value="csv" 
                                           {{ old('file_type') == 'csv' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="csv">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <i class="fas fa-file-csv fa-2x text-success mb-2"></i>
                                                <h6 class="card-title">CSV</h6>
                                                <small class="text-muted">Comma Separated Values</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="file_type" id="ofx" value="ofx" 
                                           {{ old('file_type') == 'ofx' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="ofx">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <i class="fas fa-file-code fa-2x text-warning mb-2"></i>
                                                <h6 class="card-title">OFX</h6>
                                                <small class="text-muted">Open Financial Exchange</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="file_type" id="qif" value="qif" 
                                           {{ old('file_type') == 'qif' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="qif">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <i class="fas fa-file-invoice fa-2x text-info mb-2"></i>
                                                <h6 class="card-title">QIF</h6>
                                                <small class="text-muted">Quicken Interchange</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="file_type" id="pdf" value="pdf" 
                                           {{ old('file_type') == 'pdf' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="pdf">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                <h6 class="card-title">PDF</h6>
                                                <small class="text-muted">Extrato Bancário PDF</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="file_type" id="excel" value="excel" 
                                           {{ old('file_type') == 'excel' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="excel">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <i class="fas fa-file-excel fa-2x text-success mb-2"></i>
                                                <h6 class="card-title">Excel</h6>
                                                <small class="text-muted">Planilha XLS/XLSX</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('file_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Upload de Arquivo -->
                    <div class="mb-4">
                        <label for="file" class="form-label fw-bold">
                            <i class="fas fa-cloud-upload-alt text-success me-2"></i>
                            Selecionar Arquivo *
                        </label>
                        <div class="dropzone-wrapper">
                            <div class="dropzone" id="fileDropzone">
                                <div class="dropzone-content text-center p-4">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Arraste e solte o arquivo aqui</h5>
                                    <p class="text-muted mb-3">ou</p>
                                    <input type="file" name="file" id="file" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           accept=".csv,.txt,.ofx,.qif,.pdf,.xls,.xlsx" required style="display: none;">
                                    <button type="button" class="btn btn-outline-primary btn-lg" onclick="document.getElementById('file').click()">
                                        <i class="fas fa-folder-open me-2"></i>
                                        Escolher Arquivo
                                    </button>
                                </div>
                                <div class="file-info" id="fileInfo" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file fa-2x text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1" id="fileName"></h6>
                                                <small class="text-muted" id="fileSize"></small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle text-primary"></i>
                            Tamanho máximo: <strong>20MB</strong>. Formatos aceitos: <strong>CSV, OFX, QIF, PDF, Excel (XLS/XLSX)</strong>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('imports.index') }}" class="bc-btn-secondary btn btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="bc-btn-primary btn btn-lg px-4" id="submitBtn">
                            <i class="fas fa-upload me-2"></i>
                            Iniciar Importação
                            <div class="spinner-border spinner-border-sm ms-2 d-none" id="submitSpinner"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-xl-4 col-lg-5">
        <!-- Instruções -->
        <div class="bc-card mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle text-info me-2"></i>
                    Como Importar
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <span class="timeline-number">1</span>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Selecione a Conta</h6>
                            <p class="timeline-text">Escolha a conta bancária correspondente</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <span class="timeline-number">2</span>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Escolha o Formato</h6>
                            <p class="timeline-text">Selecione CSV, OFX, QIF, PDF ou Excel</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <span class="timeline-number">3</span>
                        </div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Envie o Arquivo</h6>
                            <p class="timeline-text">Faça upload do seu extrato</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações dos Formatos -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Formatos Suportados
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="formatAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#csvInfo">
                                <i class="fas fa-file-csv text-success me-2"></i>
                                CSV (Comma Separated Values)
                            </button>
                        </h2>
                        <div id="csvInfo" class="accordion-collapse collapse" data-bs-parent="#formatAccordion">
                            <div class="accordion-body">
                                <p class="small mb-2">Arquivo de texto com valores separados por vírgula</p>
                                <p class="small mb-3"><strong>Colunas necessárias:</strong> Data, Descrição, Valor</p>
                                <a href="{{ route('imports.download-template', 'csv') }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download me-1"></i> Template CSV
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ofxInfo">
                                <i class="fas fa-file-code text-warning me-2"></i>
                                OFX (Open Financial Exchange)
                            </button>
                        </h2>
                        <div id="ofxInfo" class="accordion-collapse collapse" data-bs-parent="#formatAccordion">
                            <div class="accordion-body">
                                <p class="small mb-2">Formato padrão dos bancos brasileiros</p>
                                <p class="small mb-0">Exportado diretamente do internet banking</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qifInfo">
                                <i class="fas fa-file-invoice text-info me-2"></i>
                                QIF (Quicken Interchange)
                            </button>
                        </h2>
                        <div id="qifInfo" class="accordion-collapse collapse" data-bs-parent="#formatAccordion">
                            <div class="accordion-body">
                                <p class="small mb-2">Formato do software Quicken</p>
                                <p class="small mb-0">Suportado por diversos softwares financeiros</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pdfInfo">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                PDF (Extrato Bancário)
                            </button>
                        </h2>
                        <div id="pdfInfo" class="accordion-collapse collapse" data-bs-parent="#formatAccordion">
                            <div class="accordion-body">
                                <p class="small mb-2">Extratos bancários em formato PDF</p>
                                <p class="small mb-3"><strong>Funcionalidade:</strong> Extração automática via OCR</p>
                                <div class="alert alert-info alert-sm">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <small>Recomendamos usar CSV ou OFX para melhor precisão</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#excelInfo">
                                <i class="fas fa-file-excel text-success me-2"></i>
                                Excel (Planilhas XLS/XLSX)
                            </button>
                        </h2>
                        <div id="excelInfo" class="accordion-collapse collapse" data-bs-parent="#formatAccordion">
                            <div class="accordion-body">
                                <p class="small mb-2">Planilhas do Microsoft Excel ou LibreOffice</p>
                                <p class="small mb-3"><strong>Estrutura:</strong> Primeira linha como cabeçalho</p>
                                <div class="alert alert-warning alert-sm">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <small>Para melhor compatibilidade, salve como CSV</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas Importantes -->
        <div class="alert alert-info">
            <div class="d-flex">
                <i class="fas fa-shield-alt fa-2x text-info me-3"></i>
                <div>
                    <h6 class="alert-heading">Detecção de Duplicatas</h6>
                    <p class="mb-0 small">O sistema detecta automaticamente transações duplicadas e não as importa novamente</p>
                </div>
            </div>
        </div>

        <div class="alert alert-warning">
            <div class="d-flex">
                <i class="fas fa-clock fa-2x text-warning me-3"></i>
                <div>
                    <h6 class="alert-heading">Processamento</h6>
                    <p class="mb-0 small">Arquivos grandes podem levar alguns minutos para processar. Você receberá uma notificação quando concluído</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const dropzone = document.getElementById('fileDropzone');
    const fileInfo = document.getElementById('fileInfo');
    const dropzoneContent = dropzone.querySelector('.dropzone-content');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const form = document.getElementById('importForm');

    // Drag and drop
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            showFileInfo(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showFileInfo(this.files[0]);
        }
    });

    function showFileInfo(file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        
        dropzoneContent.style.display = 'none';
        fileInfo.style.display = 'block';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    window.clearFile = function() {
        fileInput.value = '';
        dropzoneContent.style.display = 'block';
        fileInfo.style.display = 'none';
    };

    // Form submission
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter para submeter
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            if (form.checkValidity()) {
                form.submit();
            }
        }
    });

    // Validação em tempo real
    const requiredFields = form.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('change', updateSubmitButton);
        field.addEventListener('input', updateSubmitButton);
    });

    function updateSubmitButton() {
        const isValid = form.checkValidity();
        submitBtn.disabled = !isValid;
        
        if (isValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Verificação inicial
    updateSubmitButton();
});
</script>
        </div>
    </div>
</div>
@endsection