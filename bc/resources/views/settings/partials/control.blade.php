@switch($setting->type)
    @case('string')
        <input type="text" 
               class="form-control" 
               name="settings[{{ $setting->key }}]" 
               value="{{ $setting->value }}" 
               placeholder="{{ $setting->description }}">
        @break

    @case('integer')
        <input type="number" 
               class="form-control" 
               name="settings[{{ $setting->key }}]" 
               value="{{ $setting->value }}" 
               placeholder="{{ $setting->description }}">
        @break

    @case('textarea')
        <textarea class="form-control" 
                  name="settings[{{ $setting->key }}]" 
                  rows="4" 
                  placeholder="{{ $setting->description }}">{{ $setting->value }}</textarea>
        @break

    @case('boolean')
        <div class="switch-container">
            <label class="switch">
                <input type="hidden" name="settings[{{ $setting->key }}]" value="false">
                <input type="checkbox" 
                       name="settings[{{ $setting->key }}]" 
                       value="true" 
                       {{ $setting->value == 'true' || $setting->value == '1' ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
            <span class="switch-label">
                {{ $setting->value == 'true' || $setting->value == '1' ? 'Ativado' : 'Desativado' }}
            </span>
        </div>
        @break

    @case('color')
        <div class="d-flex align-items-center gap-3">
            <input type="color" 
                   class="color-input" 
                   name="settings[{{ $setting->key }}]" 
                   value="{{ $setting->value }}" 
                   title="Escolha uma cor">
            <div class="flex-grow-1">
                <input type="text" 
                       class="form-control" 
                       value="{{ $setting->value }}" 
                       readonly 
                       style="font-family: monospace;">
            </div>
            <div class="color-preview" 
                 style="width: 40px; height: 40px; border-radius: 8px; background: {{ $setting->value }}; border: 2px solid #e2e8f0;">
            </div>
        </div>
        @break

    @case('select')
        <select class="form-select" name="settings[{{ $setting->key }}]">
            @if($setting->options)
                @foreach($setting->options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" 
                            {{ $setting->value == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @endif
        </select>
        @break

    @case('file')
        <div class="file-upload-container">
            <div class="current-file mb-3">
                @if($setting->value)
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        @if(Str::contains($setting->value, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ Storage::url($setting->value) }}" 
                                 alt="Current file" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <i class="fas fa-file fa-2x text-muted"></i>
                        @endif
                        <div class="flex-grow-1">
                            <strong>Arquivo atual:</strong><br>
                            <span class="text-muted">{{ basename($setting->value) }}</span>
                        </div>
                        <a href="{{ Storage::url($setting->value) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @else
                    <div class="text-muted">Nenhum arquivo carregado</div>
                @endif
            </div>
            
            <input type="file" 
                   class="form-control" 
                   name="file_{{ $setting->key }}" 
                   accept="image/*,application/pdf,.doc,.docx,.txt">
            <small class="form-text text-muted">
                Formatos aceitos: Imagens, PDF, DOC, DOCX, TXT (máx: 5MB)
            </small>
        </div>
        @break

    @default
        <input type="text" 
               class="form-control" 
               name="settings[{{ $setting->key }}]" 
               value="{{ $setting->value }}" 
               placeholder="{{ $setting->description }}">
@endswitch

@if($setting->type === 'color')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.querySelector('input[name="settings[{{ $setting->key }}]"]');
        const textInput = colorInput.nextElementSibling.querySelector('input[type="text"]');
        const preview = document.querySelector('.color-preview');
        
        colorInput.addEventListener('change', function() {
            textInput.value = this.value;
            preview.style.background = this.value;
        });
        
        textInput.addEventListener('change', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
                preview.style.background = this.value;
            }
        });
    });
</script>
@endif

@if($setting->type === 'boolean')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.querySelector('input[name="settings[{{ $setting->key }}]"][type="checkbox"]');
        const label = document.querySelector('.switch-label');
        
        checkbox.addEventListener('change', function() {
            label.textContent = this.checked ? 'Ativado' : 'Desativado';
        });
    });
</script>
@endif
