<!-- Modal de Configuração Personalizada -->
<div class="modal fade custom-setting-modal" id="customSettingModal" tabindex="-1" aria-labelledby="customSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white" id="customSettingModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nova Configuração Personalizada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.create-custom') }}" method="POST" id="customSettingForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-cog me-2"></i>Informações Básicas
                            </h6>
                            
                            <div class="mb-3">
                                <label for="custom_key" class="form-label">Chave da Configuração *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="custom_key" 
                                       name="key" 
                                       placeholder="ex: minha_configuracao_especial" 
                                       pattern="[a-z_]+" 
                                       required>
                                <div class="form-text">
                                    Apenas letras minúsculas e underscore. Deve ser única.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="custom_label" class="form-label">Nome da Configuração *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="custom_label" 
                                       name="label" 
                                       placeholder="ex: Minha Configuração Especial" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="custom_description" class="form-label">Descrição</label>
                                <textarea class="form-control" 
                                          id="custom_description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Descreva para que serve esta configuração..."></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="custom_type" class="form-label">Tipo de Campo *</label>
                                        <select class="form-select" id="custom_type" name="type" required>
                                            <option value="">Selecione o tipo</option>
                                            <option value="string">Texto Simples</option>
                                            <option value="textarea">Texto Longo</option>
                                            <option value="integer">Número</option>
                                            <option value="boolean">Sim/Não</option>
                                            <option value="color">Cor</option>
                                            <option value="select">Lista de Opções</option>
                                            <option value="file">Arquivo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="custom_category" class="form-label">Categoria *</label>
                                        <select class="form-select" id="custom_category" name="category" required>
                                            <option value="">Selecione a categoria</option>
                                            <option value="general">Configurações Gerais</option>
                                            <option value="appearance">Aparência e Tema</option>
                                            <option value="dashboard">Dashboard</option>
                                            <option value="modules">Módulos do Sistema</option>
                                            <option value="advanced">Configurações Avançadas</option>
                                            <option value="custom">Personalizadas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="custom_value" class="form-label">Valor Padrão</label>
                                <div id="value-control">
                                    <input type="text" 
                                           class="form-control" 
                                           id="custom_value" 
                                           name="value" 
                                           placeholder="Valor inicial da configuração">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sliders-h me-2"></i>Configurações Avançadas
                            </h6>
                            
                            <div class="mb-3" id="options-container" style="display: none;">
                                <label class="form-label">Opções (para lista)</label>
                                <div id="options-list">
                                    <div class="option-item d-flex gap-2 mb-2">
                                        <input type="text" class="form-control" placeholder="Valor" name="option_values[]">
                                        <input type="text" class="form-control" placeholder="Rótulo" name="option_labels[]">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addOption()">
                                    <i class="fas fa-plus me-2"></i>Adicionar Opção
                                </button>
                                <input type="hidden" name="options" id="options_json">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="custom_is_public" 
                                           name="is_public" 
                                           value="1">
                                    <label class="form-check-label" for="custom_is_public">
                                        <strong>Configuração Pública</strong>
                                        <div class="form-text">Se marcado, esta configuração estará disponível para uso público (ex: cores, logos).</div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="custom_sort_order" class="form-label">Ordem de Exibição</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="custom_sort_order" 
                                       name="sort_order" 
                                       value="0" 
                                       min="0" 
                                       max="1000">
                                <div class="form-text">
                                    Números menores aparecem primeiro. Use 0 para ordem padrão.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-eye me-2"></i>Pré-visualização
                                </h6>
                                <div class="preview-area">
                                    <div id="setting-preview">
                                        <div class="text-muted">
                                            <i class="fas fa-cog fa-2x mb-2"></i>
                                            <p>Configure os campos acima para ver a pré-visualização</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="previewCustomSetting()">
                        <i class="fas fa-eye me-2"></i>Pré-visualizar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Criar Configuração
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('custom_type');
        const valueControl = document.getElementById('value-control');
        const optionsContainer = document.getElementById('options-container');
        
        typeSelect.addEventListener('change', function() {
            updateValueControl(this.value);
            updatePreview();
        });
        
        // Outros campos que afetam a preview
        ['custom_label', 'custom_description', 'custom_value'].forEach(id => {
            document.getElementById(id).addEventListener('input', updatePreview);
        });
    });
    
    function updateValueControl(type) {
        const valueControl = document.getElementById('value-control');
        const optionsContainer = document.getElementById('options-container');
        
        let html = '';
        
        switch(type) {
            case 'boolean':
                html = `
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="custom_value" name="value" value="true">
                        <label class="form-check-label" for="custom_value">Ativado por padrão</label>
                    </div>
                `;
                optionsContainer.style.display = 'none';
                break;
                
            case 'color':
                html = `
                    <div class="d-flex gap-2">
                        <input type="color" class="form-control" id="custom_value" name="value" value="#667eea" style="width: 80px;">
                        <input type="text" class="form-control" value="#667eea" readonly>
                    </div>
                `;
                optionsContainer.style.display = 'none';
                break;
                
            case 'select':
                html = `<input type="hidden" id="custom_value" name="value">`;
                optionsContainer.style.display = 'block';
                break;
                
            case 'textarea':
                html = `<textarea class="form-control" id="custom_value" name="value" rows="3" placeholder="Valor inicial..."></textarea>`;
                optionsContainer.style.display = 'none';
                break;
                
            case 'integer':
                html = `<input type="number" class="form-control" id="custom_value" name="value" placeholder="0">`;
                optionsContainer.style.display = 'none';
                break;
                
            default:
                html = `<input type="text" class="form-control" id="custom_value" name="value" placeholder="Valor inicial da configuração">`;
                optionsContainer.style.display = 'none';
        }
        
        valueControl.innerHTML = html;
    }
    
    function addOption() {
        const optionsList = document.getElementById('options-list');
        const newOption = document.createElement('div');
        newOption.className = 'option-item d-flex gap-2 mb-2';
        newOption.innerHTML = `
            <input type="text" class="form-control" placeholder="Valor" name="option_values[]">
            <input type="text" class="form-control" placeholder="Rótulo" name="option_labels[]">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOption(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        optionsList.appendChild(newOption);
        updateOptionsJson();
    }
    
    function removeOption(button) {
        button.closest('.option-item').remove();
        updateOptionsJson();
    }
    
    function updateOptionsJson() {
        const values = Array.from(document.querySelectorAll('input[name="option_values[]"]')).map(i => i.value);
        const labels = Array.from(document.querySelectorAll('input[name="option_labels[]"]')).map(i => i.value);
        
        const options = {};
        values.forEach((value, index) => {
            if (value && labels[index]) {
                options[value] = labels[index];
            }
        });
        
        document.getElementById('options_json').value = JSON.stringify(options);
    }
    
    function previewCustomSetting() {
        updatePreview();
    }
    
    function updatePreview() {
        const preview = document.getElementById('setting-preview');
        const label = document.getElementById('custom_label').value || 'Nome da Configuração';
        const description = document.getElementById('custom_description').value || 'Descrição da configuração';
        const type = document.getElementById('custom_type').value || 'string';
        
        let controlHtml = '';
        
        switch(type) {
            case 'boolean':
                controlHtml = `
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label">Ativado/Desativado</label>
                    </div>
                `;
                break;
            case 'color':
                controlHtml = `
                    <div class="d-flex gap-2 align-items-center">
                        <div style="width: 40px; height: 40px; background: #667eea; border-radius: 8px; border: 2px solid #e2e8f0;"></div>
                        <input type="text" class="form-control" value="#667eea" readonly style="font-family: monospace;">
                    </div>
                `;
                break;
            case 'select':
                controlHtml = `
                    <select class="form-select" disabled>
                        <option>Opção 1</option>
                        <option>Opção 2</option>
                    </select>
                `;
                break;
            case 'textarea':
                controlHtml = `<textarea class="form-control" rows="3" placeholder="Texto longo..." disabled></textarea>`;
                break;
            case 'integer':
                controlHtml = `<input type="number" class="form-control" placeholder="0" disabled>`;
                break;
            default:
                controlHtml = `<input type="text" class="form-control" placeholder="Texto..." disabled>`;
        }
        
        preview.innerHTML = `
            <div class="setting-item">
                <div class="setting-label mb-2">
                    <i class="fas fa-cog text-muted me-2"></i>
                    <strong>${label}</strong>
                </div>
                <div class="setting-description text-muted mb-3" style="font-size: 0.9rem;">
                    ${description}
                </div>
                <div class="setting-control">
                    ${controlHtml}
                </div>
            </div>
        `;
    }
</script>
