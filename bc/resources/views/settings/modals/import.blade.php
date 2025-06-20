<!-- Modal de Importação -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-upload me-2"></i>Importar Configurações
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> 
                        O arquivo deve estar no formato JSON e conter configurações válidas. 
                        As configurações atuais serão substituídas pelas do arquivo.
                    </div>
                    
                    <div class="mb-3">
                        <label for="settings_file" class="form-label">Arquivo de Configurações</label>
                        <input type="file" 
                               class="form-control" 
                               id="settings_file" 
                               name="settings_file" 
                               accept=".json" 
                               required>
                        <div class="form-text">
                            Selecione um arquivo JSON com as configurações para importar.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="backup_current" 
                                   name="backup_current" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label" for="backup_current">
                                Fazer backup das configurações atuais antes de importar
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> 
                        Esta ação não pode ser desfeita. Certifique-se de que o arquivo está correto.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Importar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
