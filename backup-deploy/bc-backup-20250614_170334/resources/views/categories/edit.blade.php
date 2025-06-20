@extends('layouts.app')

@section('title', 'Editar Categoria - ' . $category->name)

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-edit text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Editar Categoria</h2>
        <p class="text-muted mb-0">Atualize as informações da categoria "{{ $category->name }}"</p>
    </div>
</div>
@endsection

@section('header-actions')
<div class="d-flex gap-2">
    <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary">
        <i class="fas fa-eye me-2"></i>Visualizar
    </a>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Formulário Principal -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Dados da Categoria
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST" id="categoryForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag text-primary me-2"></i>Nome da Categoria
                                </label>
                                <input type="text" name="name" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $category->name) }}" 
                                       placeholder="Digite o nome da categoria..."
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nome único para identificar a categoria
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-exchange-alt text-info me-2"></i>Tipo
                                </label>
                                <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>
                                        💸 Despesa
                                    </option>
                                    <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>
                                        💰 Receita
                                    </option>
                                    <option value="both" {{ old('type', $category->type) == 'both' ? 'selected' : '' }}>
                                        🔄 Ambos
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Define o uso da categoria
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-icons text-warning me-2"></i>Ícone (Font Awesome)
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-{{ old('icon', $category->icon) ?: 'tag' }}" id="iconPreview"></i>
                                    </span>
                                    <input type="text" name="icon" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           value="{{ old('icon', $category->icon) }}" 
                                           placeholder="shopping-cart"
                                           id="iconInput">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nome do ícone Font Awesome (sem "fa-")
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-palette text-success me-2"></i>Cor
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="color" name="color" 
                                           class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           value="{{ old('color', $category->color ?: '#007bff') }}"
                                           id="colorInput">
                                    <input type="text" class="form-control" 
                                           value="{{ old('color', $category->color ?: '#007bff') }}"
                                           id="colorText" readonly>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Cor para identificação visual da categoria
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-sticky-note text-secondary me-2"></i>Descrição
                        </label>
                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Descreva o uso e características desta categoria...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Informações adicionais sobre a categoria (opcional)
                        </div>
                    </div>
                    
                    <!-- Preview da Categoria -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-eye text-info me-2"></i>Preview
                        </label>
                        <div class="card border-2" id="categoryPreview" style="border-color: {{ $category->color ?: '#007bff' }};">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-3 me-3" id="previewIconContainer">
                                        <i class="fas fa-{{ $category->icon ?: 'tag' }} fa-2x" id="previewIcon" style="color: {{ $category->color ?: '#007bff' }};"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" id="previewName">{{ $category->name }}</h5>
                                        <span class="badge" id="previewType" style="background-color: {{ $category->color ?: '#007bff' }};">
                                            @switch($category->type)
                                                @case('expense')
                                                    💸 Despesa
                                                    @break
                                                @case('income')
                                                    💰 Receita
                                                    @break
                                                @default
                                                    🔄 Ambos
                                            @endswitch
                                        </span>
                                        @if($category->description)
                                            <p class="text-muted small mb-0 mt-2" id="previewDescription">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-lg" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Restaurar
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <button type="submit" name="action" value="save_and_continue" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>Salvar e Continuar Editando
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Estatísticas da Categoria -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Estatísticas da Categoria
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $category->transactions()->count() }}</h4>
                            <small class="text-muted">Transações</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">R$ {{ number_format($category->transactions()->sum('amount'), 2, ',', '.') }}</h4>
                        <small class="text-muted">Total Movimentado</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Criada em:</span>
                        <span>{{ $category->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Última atualização:</span>
                        <span>{{ $category->updated_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-{{ $category->active ? 'success' : 'secondary' }}">
                            {{ $category->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                </div>
                
                @if($category->transactions()->count() > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong><br>
                        Esta categoria está sendo usada em {{ $category->transactions()->count() }} transação(ões).
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Transações Recentes -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>Transações Recentes
                </h6>
            </div>
            <div class="card-body">
                @if($category->transactions()->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($category->transactions()->latest()->take(5)->get() as $transaction)
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">{{ Str::limit($transaction->description, 25) }}</h6>
                                <p class="text-muted small mb-1">{{ $transaction->bankAccount->name }}</p>
                                <small class="text-muted">{{ $transaction->transaction_date->format('d/m/Y') }}</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('transactions.index', ['category' => $category->name]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Ver Todas
                    </a>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Nenhuma transação encontrada</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Seleção de Ícones -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-icons me-2"></i>Selecionar Ícone
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="iconGrid">
                    <!-- Ícones serão carregados via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

#categoryPreview {
    transition: all 0.3s ease;
}

.icon-option {
    cursor: pointer;
    padding: 10px;
    text-align: center;
    border: 2px solid transparent;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.icon-option:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.icon-option.selected {
    border-color: #0d6efd;
    background-color: #e3f2fd;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Preview em tempo real
    updatePreview();
    
    // Eventos para atualizar preview
    $('#categoryForm input, #categoryForm select, #categoryForm textarea').on('input change', function() {
        updatePreview();
    });
    
    // Sincronizar cor
    $('#colorInput').on('change', function() {
        $('#colorText').val($(this).val());
        updatePreview();
    });
    
    // Atualizar ícone
    $('#iconInput').on('input', function() {
        const iconName = $(this).val() || 'tag';
        $('#iconPreview').attr('class', `fas fa-${iconName}`);
        updatePreview();
    });
    
    // Carregar ícones populares
    loadPopularIcons();
    
    // Validação em tempo real
    $('#categoryForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });
});

function updatePreview() {
    const name = $('input[name="name"]').val() || 'Nome da Categoria';
    const type = $('select[name="type"]').val();
    const icon = $('input[name="icon"]').val() || 'tag';
    const color = $('input[name="color"]').val() || '#007bff';
    const description = $('textarea[name="description"]').val();
    
    // Atualizar elementos do preview
    $('#previewName').text(name);
    $('#previewIcon').attr('class', `fas fa-${icon} fa-2x`).css('color', color);
    $('#categoryPreview').css('border-color', color);
    
    // Atualizar tipo
    let typeText = '';
    switch(type) {
        case 'expense':
            typeText = '💸 Despesa';
            break;
        case 'income':
            typeText = '💰 Receita';
            break;
        default:
            typeText = '🔄 Ambos';
    }
    $('#previewType').text(typeText).css('background-color', color);
    
    // Atualizar descrição
    if (description) {
        if ($('#previewDescription').length === 0) {
            $('#previewType').parent().append('<p class="text-muted small mb-0 mt-2" id="previewDescription"></p>');
        }
        $('#previewDescription').text(description);
    } else {
        $('#previewDescription').remove();
    }
}

function loadPopularIcons() {
    const icons = [
        'shopping-cart', 'home', 'car', 'utensils', 'gas-pump', 'hospital',
        'graduation-cap', 'plane', 'music', 'gamepad', 'gift', 'credit-card',
        'dollar-sign', 'chart-line', 'piggy-bank', 'wallet', 'coins', 'receipt',
        'store', 'coffee', 'pizza-slice', 'hamburger', 'bus', 'subway'
    ];
    
    let iconsHtml = '';
    icons.forEach(icon => {
        iconsHtml += `
            <div class="col-2 mb-3">
                <div class="icon-option" onclick="selectIcon('${icon}')">
                    <i class="fas fa-${icon} fa-2x mb-2"></i>
                    <div class="small">${icon}</div>
                </div>
            </div>
        `;
    });
    
    $('#iconGrid').html(iconsHtml);
}

function selectIcon(iconName) {
    $('#iconInput').val(iconName);
    $('#iconPreview').attr('class', `fas fa-${iconName}`);
    updatePreview();
    $('#iconModal').modal('hide');
    
    // Marcar como selecionado
    $('.icon-option').removeClass('selected');
    $(`.icon-option:contains(${iconName})`).addClass('selected');
}

function resetForm() {
    if (confirm('Tem certeza que deseja restaurar os valores originais?')) {
        // Restaurar valores originais
        $('input[name="name"]').val('{{ $category->name }}');
        $('select[name="type"]').val('{{ $category->type }}');
        $('input[name="icon"]').val('{{ $category->icon }}');
        $('input[name="color"]').val('{{ $category->color ?: "#007bff" }}');
        $('textarea[name="description"]').val('{{ $category->description }}');
        
        updatePreview();
    }
}

function validateForm() {
    let isValid = true;
    
    // Validar nome
    const name = $('input[name="name"]').val().trim();
    if (name.length < 2) {
        showError('name', 'O nome deve ter pelo menos 2 caracteres');
        isValid = false;
    }
    
    // Validar ícone
    const icon = $('input[name="icon"]').val().trim();
    if (icon && !/^[a-z0-9\-]+$/.test(icon)) {
        showError('icon', 'Nome do ícone inválido');
        isValid = false;
    }
    
    return isValid;
}

function showError(field, message) {
    const input = $(`[name="${field}"]`);
    input.addClass('is-invalid');
    input.siblings('.invalid-feedback').remove();
    input.after(`<div class="invalid-feedback">${message}</div>`);
}
</script>
@endpush
