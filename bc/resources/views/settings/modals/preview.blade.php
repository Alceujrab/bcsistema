<!-- Modal de Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Pré-visualização do Tema
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-lg-8">
                        <div class="preview-iframe-container h-100">
                            <iframe id="themePreview" 
                                    src="{{ route('dashboard') }}?preview=1" 
                                    class="w-100 h-100 border-0">
                            </iframe>
                        </div>
                    </div>
                    <div class="col-lg-4 bg-light border-start">
                        <div class="p-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-palette me-2"></i>Personalização em Tempo Real
                            </h6>
                            
                            <div class="preview-controls">
                                <div class="mb-3">
                                    <label class="form-label">Cor Primária</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" 
                                               class="preview-color-input" 
                                               data-setting="primary_color" 
                                               value="#667eea">
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               value="#667eea" 
                                               readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cor Secundária</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" 
                                               class="preview-color-input" 
                                               data-setting="secondary_color" 
                                               value="#764ba2">
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               value="#764ba2" 
                                               readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cor de Sucesso</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" 
                                               class="preview-color-input" 
                                               data-setting="success_color" 
                                               value="#28a745">
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               value="#28a745" 
                                               readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cor de Perigo</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" 
                                               class="preview-color-input" 
                                               data-setting="danger_color" 
                                               value="#dc3545">
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               value="#dc3545" 
                                               readonly>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cards por Linha</label>
                                    <select class="form-select form-select-sm" data-setting="dashboard_cards_per_row">
                                        <option value="2">2 Cards</option>
                                        <option value="3" selected>3 Cards</option>
                                        <option value="4">4 Cards</option>
                                        <option value="6">6 Cards</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="preview_welcome_banner" 
                                               data-setting="show_welcome_banner" 
                                               checked>
                                        <label class="form-check-label" for="preview_welcome_banner">
                                            Mostrar Banner de Boas-vindas
                                        </label>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6 class="text-muted mb-3">Temas Pré-definidos</h6>
                                
                                <div class="theme-presets">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm me-2 mb-2" 
                                            onclick="applyTheme('default')">
                                        Padrão
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm me-2 mb-2" 
                                            onclick="applyTheme('nature')">
                                        Natureza
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm me-2 mb-2" 
                                            onclick="applyTheme('sunset')">
                                        Pôr do Sol
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-info btn-sm me-2 mb-2" 
                                            onclick="applyTheme('ocean')">
                                        Oceano
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-dark btn-sm me-2 mb-2" 
                                            onclick="applyTheme('dark')">
                                        Escuro
                                    </button>
                                </div>
                                
                                <hr>
                                
                                <div class="d-grid gap-2">
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="applyPreviewChanges()">
                                        <i class="fas fa-check me-2"></i>Aplicar Mudanças
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-secondary" 
                                            onclick="resetPreview()">
                                        <i class="fas fa-undo me-2"></i>Resetar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Temas pré-definidos
    const themes = {
        default: {
            primary_color: '#667eea',
            secondary_color: '#764ba2',
            success_color: '#28a745',
            danger_color: '#dc3545',
            warning_color: '#ffc107',
            info_color: '#17a2b8'
        },
        nature: {
            primary_color: '#27ae60',
            secondary_color: '#2ecc71',
            success_color: '#16a085',
            danger_color: '#e74c3c',
            warning_color: '#f39c12',
            info_color: '#3498db'
        },
        sunset: {
            primary_color: '#e74c3c',
            secondary_color: '#f39c12',
            success_color: '#27ae60',
            danger_color: '#c0392b',
            warning_color: '#f1c40f',
            info_color: '#9b59b6'
        },
        ocean: {
            primary_color: '#3498db',
            secondary_color: '#2980b9',
            success_color: '#1abc9c',
            danger_color: '#e74c3c',
            warning_color: '#f39c12',
            info_color: '#16a085'
        },
        dark: {
            primary_color: '#2c3e50',
            secondary_color: '#34495e',
            success_color: '#27ae60',
            danger_color: '#e74c3c',
            warning_color: '#f39c12',
            info_color: '#3498db'
        }
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar event listeners para os controles de preview
        const previewInputs = document.querySelectorAll('.preview-color-input, select[data-setting], input[data-setting]');
        previewInputs.forEach(input => {
            input.addEventListener('change', function() {
                updatePreviewFrame(this.dataset.setting, this.value);
            });
        });
        
        // Sincronizar inputs de cor com campos de texto
        const colorInputs = document.querySelectorAll('.preview-color-input');
        colorInputs.forEach(input => {
            const textInput = input.nextElementSibling;
            
            input.addEventListener('change', function() {
                textInput.value = this.value;
            });
            
            textInput.addEventListener('change', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    input.value = this.value;
                    updatePreviewFrame(input.dataset.setting, this.value);
                }
            });
        });
    });
    
    function updatePreviewFrame(setting, value) {
        const iframe = document.getElementById('themePreview');
        
        // Enviar mensagem para o iframe
        iframe.contentWindow.postMessage({
            action: 'updateSetting',
            setting: setting,
            value: value
        }, '*');
    }
    
    function applyTheme(themeName) {
        const theme = themes[themeName];
        if (!theme) return;
        
        Object.keys(theme).forEach(setting => {
            const input = document.querySelector(`[data-setting="${setting}"]`);
            if (input) {
                input.value = theme[setting];
                if (input.type === 'color') {
                    input.nextElementSibling.value = theme[setting];
                }
                updatePreviewFrame(setting, theme[setting]);
            }
        });
    }
    
    function applyPreviewChanges() {
        // Aplicar as mudanças do preview ao formulário principal
        const previewInputs = document.querySelectorAll('[data-setting]');
        previewInputs.forEach(input => {
            const mainInput = document.querySelector(`[name="settings[${input.dataset.setting}]"]`);
            if (mainInput) {
                if (input.type === 'checkbox') {
                    mainInput.checked = input.checked;
                } else {
                    mainInput.value = input.value;
                }
                
                // Disparar evento change para atualizar outros elementos
                mainInput.dispatchEvent(new Event('change'));
            }
        });
        
        // Fechar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
        modal.hide();
        
        // Mostrar toast de sucesso
        showToast('Mudanças aplicadas com sucesso!', 'success');
    }
    
    function resetPreview() {
        // Resetar preview para valores originais
        const iframe = document.getElementById('themePreview');
        iframe.src = iframe.src; // Recarregar iframe
        
        // Resetar controles
        applyTheme('default');
    }
    
    function showToast(message, type = 'info') {
        // Implementar sistema de toast/notificação
        alert(message); // Temporário
    }
</script>
