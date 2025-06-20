@extends('layouts.app')

@section('title', 'Nova Importação de Extrato')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cloud-upload-alt text-success me-2"></i>
            Nova Importação de Extrato
        </h1>
        <p class="text-muted mb-0">Upload de arquivos bancários em múltiplos formatos</p>
    </div>
    <a href="{{ route('extract-imports.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Formulário de Upload -->
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-gradient-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-upload me-2"></i>Upload de Arquivo
                </h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Erros encontrados:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('extract-imports.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <!-- Seleção da Conta -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="bank_account_id" class="form-label">
                                <i class="fas fa-university text-primary me-1"></i>
                                Conta Bancária *
                            </label>
                            <select name="bank_account_id" id="bank_account_id" class="form-select" required>
                                <option value="">Selecione a conta...</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->bank_name }} - {{ $account->account_number }}
                                        ({{ $account->account_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_type" class="form-label">
                                <i class="fas fa-credit-card text-info me-1"></i>
                                Tipo de Extrato *
                            </label>
                            <select name="import_type" id="import_type" class="form-select" required>
                                <option value="">Selecione o tipo...</option>
                                <option value="bank_statement" {{ old('import_type') == 'bank_statement' ? 'selected' : '' }}>
                                    <i class="fas fa-university me-1"></i>Extrato Bancário
                                </option>
                                <option value="credit_card" {{ old('import_type') == 'credit_card' ? 'selected' : '' }}>
                                    <i class="fas fa-credit-card me-1"></i>Fatura de Cartão
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Upload do Arquivo -->
                    <div class="mb-4">
                        <label for="file" class="form-label">
                            <i class="fas fa-file text-warning me-1"></i>
                            Arquivo do Extrato *
                        </label>
                        <div class="input-group">                        <input type="file" name="file" id="file" class="form-control" 
                               accept=".csv,.txt,.ofx,.qif,.pdf,.xls,.xlsx,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                            <button type="button" class="btn btn-outline-info" onclick="showFormatInfo()">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Formatos aceitos: CSV, TXT, OFX, QIF, PDF, XLS, XLSX (máx. 10MB)
                        </div>
                    </div>

                    <!-- Opções Avançadas -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <a href="#advancedOptions" data-bs-toggle="collapse" class="text-decoration-none">
                                    <i class="fas fa-cogs text-secondary me-2"></i>
                                    Opções Avançadas
                                    <i class="fas fa-chevron-down float-end mt-1"></i>
                                </a>
                            </h6>
                        </div>
                        <div id="advancedOptions" class="collapse">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="encoding" class="form-label">Codificação</label>
                                        <select name="encoding" id="encoding" class="form-select">
                                            <option value="">Auto-detecção</option>
                                            <option value="UTF-8">UTF-8</option>
                                            <option value="ISO-8859-1">ISO-8859-1</option>
                                            <option value="Windows-1252">Windows-1252</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="delimiter" class="form-label">Separador (CSV)</label>
                                        <select name="delimiter" id="delimiter" class="form-select">
                                            <option value="">Auto-detecção</option>
                                            <option value=";">Ponto e vírgula (;)</option>
                                            <option value=",">Vírgula (,)</option>
                                            <option value="|">Pipe (|)</option>
                                            <option value="	">Tab</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="date_format" class="form-label">Formato de Data</label>
                                        <select name="date_format" id="date_format" class="form-select">
                                            <option value="">Auto-detecção</option>
                                            <option value="d/m/Y">DD/MM/AAAA</option>
                                            <option value="m/d/Y">MM/DD/AAAA</option>
                                            <option value="Y-m-d">AAAA-MM-DD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="checkbox" name="skip_duplicates" id="skip_duplicates" 
                                                   class="form-check-input" value="1" checked>
                                            <label for="skip_duplicates" class="form-check-label">
                                                <i class="fas fa-shield-alt text-success me-1"></i>
                                                Ignorar transações duplicadas
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-info" onclick="validateFile()">
                            <i class="fas fa-search me-2"></i>Validar Arquivo
                        </button>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Importar Extrato
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Painel de Ajuda -->
    <div class="col-lg-4">
        <!-- Formatos Suportados -->
        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-alt me-2"></i>Formatos Suportados
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-csv fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-1">CSV / TXT</h6>
                            <small class="text-muted">Mais comum, suporte completo</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-code fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="mb-1">OFX</h6>
                            <small class="text-muted">Padrão bancário internacional</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="mb-1">QIF</h6>
                            <small class="text-muted">Formato Quicken</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                        <div>
                            <h6 class="mb-1">PDF</h6>
                            <small class="text-muted">Extração inteligente</small>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-file-excel fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-1">Excel</h6>
                            <small class="text-muted">XLS / XLSX</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bancos Suportados -->
        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-university me-2"></i>Bancos Brasileiros
                </h6>
            </div>
            <div class="card-body">
                @foreach($bankPatterns as $bank => $pattern)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span class="text-capitalize">{{ ucfirst($bank) }}</span>
                </div>
                @endforeach
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-magic me-2"></i>
                    <small>Detecção automática baseada no conteúdo do arquivo</small>
                </div>
            </div>
        </div>

        <!-- Templates -->
        <div class="card shadow">
            <div class="card-header bg-gradient-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-download me-2"></i>Templates
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('extract-imports.template', 'csv') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-csv me-2"></i>Template CSV
                    </a>
                    <a href="{{ route('extract-imports.template', 'ofx') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-code me-2"></i>Exemplo OFX
                    </a>
                    <a href="{{ route('extract-imports.template', 'qif') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-file-alt me-2"></i>Exemplo QIF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Informações de Formato -->
<div class="modal fade" id="formatInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Informações dos Formatos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-file-csv text-success me-2"></i>CSV/TXT</h6>
                        <ul class="small">
                            <li>Separado por vírgula, ponto-vírgula ou pipe</li>
                            <li>Primeira linha deve conter os cabeçalhos</li>
                            <li>Colunas: Data, Descrição, Valor, Saldo</li>
                            <li>Formato de data: DD/MM/AAAA</li>
                        </ul>

                        <h6><i class="fas fa-file-code text-info me-2"></i>OFX</h6>
                        <ul class="small">
                            <li>Padrão Open Financial Exchange</li>
                            <li>Formato XML estruturado</li>
                            <li>Suporte nativo dos bancos</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-file-alt text-warning me-2"></i>QIF</h6>
                        <ul class="small">
                            <li>Formato Quicken Interchange</li>
                            <li>Texto estruturado por linhas</li>
                            <li>Compatível com vários softwares</li>
                        </ul>

                        <h6><i class="fas fa-file-pdf text-danger me-2"></i>PDF</h6>
                        <ul class="small">
                            <li>Extração automática de texto</li>
                            <li>Reconhecimento de padrões bancários</li>
                            <li>Pode ter limitações dependendo do layout</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showFormatInfo() {
    new bootstrap.Modal(document.getElementById('formatInfoModal')).show();
}

function validateFile() {
    const fileInput = document.getElementById('file');
    const bankAccount = document.getElementById('bank_account_id').value;
    
    if (!fileInput.files[0]) {
        Swal.fire('Erro', 'Selecione um arquivo primeiro', 'error');
        return;
    }
    
    if (!bankAccount) {
        Swal.fire('Erro', 'Selecione uma conta bancária', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('bank_account_id', bankAccount);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    Swal.fire({
        title: 'Validando arquivo...',
        text: 'Aguarde enquanto analisamos o arquivo',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("extract-imports.validate") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Arquivo válido!',
                html: `
                    <div class="text-start">
                        <p><strong>Formato:</strong> ${data.format}</p>
                        <p><strong>Registros encontrados:</strong> ${data.count}</p>
                        <p><strong>Banco detectado:</strong> ${data.detected_bank || 'Não identificado'}</p>
                        ${data.encoding ? `<p><strong>Codificação:</strong> ${data.encoding}</p>` : ''}
                        ${data.delimiter ? `<p><strong>Separador:</strong> ${data.delimiter}</p>` : ''}
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Continuar com a importação'
            });
        } else {
            Swal.fire('Erro na validação', data.error, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Erro', 'Falha na validação do arquivo', 'error');
        console.error('Error:', error);
    });
}

// Melhorar UX do upload
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileType = file.name.split('.').pop().toUpperCase();
        
        // Mostrar informações do arquivo
        const info = document.createElement('div');
        info.className = 'alert alert-info mt-2';
        info.innerHTML = `
            <i class="fas fa-file me-2"></i>
            <strong>${file.name}</strong> (${fileType}, ${fileSize} MB)
        `;
        
        // Remover info anterior se existir
        const existingInfo = document.querySelector('.file-info');
        if (existingInfo) {
            existingInfo.remove();
        }
        
        info.classList.add('file-info');
        e.target.parentNode.parentNode.appendChild(info);
    }
});

// Submissão do formulário com loading
document.getElementById('importForm').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
    
    Swal.fire({
        title: 'Processando importação...',
        text: 'Aguarde enquanto processamos seu arquivo',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
});
</script>
@endsection
