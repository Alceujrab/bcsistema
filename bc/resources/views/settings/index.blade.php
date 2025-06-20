@extends('layouts.app')

@section('title', 'Configurações do Sistema')

@section('styles')
<style>
    .settings-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .settings-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .settings-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: float 20s infinite linear;
    }
    
    .settings-nav {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        position: sticky;
        top: 20px;
        height: fit-content;
    }
    
    .settings-nav .nav-link {
        color: #4a5568 !important;
        background: #f7fafc !important;
        padding: 1rem 1.5rem !important;
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 0 !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        font-weight: 500 !important;
        position: relative !important;
    }
    
    .settings-nav .nav-link:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
        color: #667eea !important;
        transform: translateX(5px) !important;
        border-left: 4px solid #667eea !important;
    }
    
    .settings-nav .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        transform: translateX(5px) !important;
        font-weight: 600 !important;
        border-left: 4px solid #5a67d8 !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
    }
    
    .settings-nav .nav-link.active:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%) !important;
        color: white !important;
    }
    
    .settings-nav .nav-link:last-child {
        border-bottom: none;
    }
    
    .settings-content {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        padding: 2.5rem;
        min-height: 600px;
    }
    
    .setting-group {
        margin-bottom: 2.5rem;
        padding: 2rem;
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 15px;
        border: 2px solid #e2e8f0;
        position: relative;
    }
    
    .setting-group::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px 15px 0 0;
    }
    
    .setting-group h4 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .setting-item {
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .setting-item:hover {
        border-color: #cbd5e0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    
    .setting-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .setting-description {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .form-control,
    .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .color-input {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        border: 3px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .color-input:hover {
        border-color: #cbd5e0;
        transform: scale(1.1);
    }
    
    .switch-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    
    .action-buttons {
        position: sticky;
        bottom: 20px;
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
        margin-top: 2rem;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    
    .btn-outline-secondary {
        border: 2px solid #6c757d;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
    }
    
    .btn-outline-danger {
        border: 2px solid #dc3545;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
    }
    
    .custom-setting-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .preview-area {
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px dashed #cbd5e0;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        margin-top: 1rem;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .theme-preview {
        width: 100%;
        max-width: 400px;
        height: 200px;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    @keyframes float {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    @media (max-width: 768px) {
        .settings-container {
            padding: 1rem 0;
        }
        
        .settings-content,
        .setting-group {
            padding: 1.5rem;
        }
        
        .action-buttons {
            position: relative;
            bottom: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="settings-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="settings-header">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="mb-3">
                        <i class="fas fa-cogs me-3"></i>Configurações do Sistema
                    </h1>
                    <p class="mb-0 opacity-90">
                        Personalize sua experiência, configure módulos, ajuste cores e muito mais.
                        Todas as alterações são aplicadas em tempo real.
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                        <button class="btn btn-light btn-sm" onclick="exportSettings()">
                            <i class="fas fa-download me-2"></i>Exportar
                        </button>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-upload me-2"></i>Importar
                        </button>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#customSettingModal">
                            <i class="fas fa-plus me-2"></i>Nova Config
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Navegação -->
            <div class="col-lg-3 mb-4">
                <div class="settings-nav">
                    <div class="nav flex-column nav-pills">
                        @foreach($categories as $key => $label)
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#{{ $key }}-tab" 
                                    type="button">
                                        <i class="fas fa-{{ 
                                            ['general' => 'cog', 'appearance' => 'palette', 'dashboard' => 'tachometer-alt', 'modules' => 'puzzle-piece', 'advanced' => 'tools'][$key] ?? 'cog' 
                                        }}"></i>
                                {{ $label }}
                                @if(isset($settings[$key]))
                                    <span class="badge bg-primary ms-auto">{{ count($settings[$key]) }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Conteúdo -->
            <div class="col-lg-9">
                <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                    @csrf
                    
                    <div class="settings-content">
                        <div class="tab-content">
                            @foreach($categories as $categoryKey => $categoryLabel)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                     id="{{ $categoryKey }}-tab">
                                     
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h2>
                                            <i class="fas fa-{{ 
                                                ['general' => 'cog', 'appearance' => 'palette', 'dashboard' => 'tachometer-alt', 'modules' => 'puzzle-piece', 'advanced' => 'tools'][$categoryKey] ?? 'cog' 
                                            }} me-3 text-primary"></i>
                                            {{ $categoryLabel }}
                                        </h2>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="resetCategory('{{ $categoryKey }}')">
                                            <i class="fas fa-undo me-2"></i>Restaurar Padrão
                                        </button>
                                    </div>

                                    @if(isset($settings[$categoryKey]) && count($settings[$categoryKey]) > 0)
                                        @foreach($settings[$categoryKey] as $setting)
                                            <div class="setting-item" data-setting="{{ $setting->key }}">
                                                <div class="setting-label">
                                                    <i class="fas fa-{{ 
                                                        ['string' => 'font', 'integer' => 'hashtag', 'boolean' => 'toggle-on', 'color' => 'palette', 'select' => 'list', 'textarea' => 'align-left', 'file' => 'image'][$setting->type] ?? 'cog' 
                                                    }} text-muted me-2"></i>
                                                    {{ $setting->label }}
                                                    @if($setting->is_public)
                                                        <span class="badge bg-success ms-2">Público</span>
                                                    @endif
                                                </div>
                                                
                                                @if($setting->description)
                                                    <div class="setting-description">
                                                        {{ $setting->description }}
                                                    </div>
                                                @endif

                                                <div class="setting-control">
                                                    @include('settings.partials.control', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Nenhuma configuração nesta categoria</h5>
                                            <p class="text-muted">Clique em "Nova Config" para adicionar configurações personalizadas.</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Configurações
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="previewChanges()">
                            <i class="fas fa-eye me-2"></i>Visualizar
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Desfazer Alterações
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="resetAll()">
                            <i class="fas fa-exclamation-triangle me-2"></i>Restaurar Tudo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modais -->
@include('settings.modals.import')
@include('settings.modals.custom-setting')
@include('settings.modals.preview')
@endsection

@section('scripts')
<script>
    // Aplicar mudanças em tempo real
    document.addEventListener('DOMContentLoaded', function() {
        // Aplicar cores em tempo real
        const colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(input => {
            input.addEventListener('change', function() {
                applyColorChange(this.name.replace('settings[', '').replace(']', ''), this.value);
            });
        });

        // Auto-save em mudanças
        const formElements = document.querySelectorAll('#settingsForm input, #settingsForm select');
        formElements.forEach(element => {
            element.addEventListener('change', function() {
                if (this.dataset.autosave !== 'false') {
                    autoSave();
                }
            });
        });
    });

    function applyColorChange(settingKey, color) {
        const root = document.documentElement;
        
        switch(settingKey) {
            case 'primary_color':
                root.style.setProperty('--primary-color', color);
                break;
            case 'secondary_color':
                root.style.setProperty('--secondary-color', color);
                break;
            case 'success_color':
                root.style.setProperty('--success-color', color);
                break;
            case 'danger_color':
                root.style.setProperty('--danger-color', color);
                break;
            case 'warning_color':
                root.style.setProperty('--warning-color', color);
                break;
            case 'info_color':
                root.style.setProperty('--info-color', color);
                break;
        }
        
        updatePreview();
    }

    function autoSave() {
        // Implementar auto-save
        console.log('Auto-salvando configurações...');
    }

    function exportSettings() {
        window.location.href = "{{ route('settings.export') }}";
    }

    function resetCategory(category) {
        if (confirm('Tem certeza que deseja restaurar as configurações padrão desta categoria?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('settings.reset') }}";
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            
            const categoryInput = document.createElement('input');
            categoryInput.type = 'hidden';
            categoryInput.name = 'category';
            categoryInput.value = category;
            
            form.appendChild(csrfToken);
            form.appendChild(categoryInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function resetAll() {
        if (confirm('ATENÇÃO: Esta ação irá restaurar TODAS as configurações aos valores padrão. Continuar?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('settings.reset') }}";
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function resetForm() {
        if (confirm('Deseja desfazer todas as alterações não salvas?')) {
            location.reload();
        }
    }

    function previewChanges() {
        updatePreview();
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    function updatePreview() {
        // Atualizar preview das mudanças
        const preview = document.getElementById('themePreview');
        if (preview) {
            // Implementar preview em tempo real
        }
    }
</script>
@endsection
