@extends('layouts.app')

@section('title', 'Nova Categoria')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-warning me-2"></i>
            Nova Categoria
        </h1>
        <p class="text-muted mb-0">Crie uma nova categoria para organizar suas transações</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('categories.index') }}" class="text-decoration-none">Categorias</a>
            </li>
            <li class="breadcrumb-item active">Nova Categoria</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Formulário Principal -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-warning text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-edit me-2"></i>Dados da Categoria
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                    @csrf
                    
                    <!-- Nome e Tipo -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag text-primary me-1"></i>Nome da Categoria *
                            </label>
                            <input type="text" name="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required autofocus
                                   placeholder="Ex: Alimentação, Transporte, Lazer..."
                                   maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle text-info me-1"></i>
                                Use nomes claros e específicos para facilitar a identificação
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-chart-line text-success me-1"></i>Tipo *
                            </label>
                            <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                <option value="expense" {{ old('type', 'expense') == 'expense' ? 'selected' : '' }}>
                                    💸 Despesa (Saída)
                                </option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>
                                    💰 Receita (Entrada)
                                </option>
                                <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>
                                    🔄 Ambos (Transferência)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-question-circle text-info me-1"></i>
                                Define se é para receitas, despesas ou ambos
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ícone e Cor -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-icons text-info me-1"></i>Ícone (Font Awesome)
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-" id="iconPreview">tag</i>
                                </span>
                                <span class="input-group-text">fa-</span>
                                <input type="text" name="icon" class="form-control" 
                                       value="{{ old('icon', 'tag') }}" placeholder="tag" id="iconInput">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <a href="https://fontawesome.com/icons" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver todos os ícones disponíveis
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-palette text-warning me-1"></i>Cor da Categoria *
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="color" name="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       value="{{ old('color', '#6c757d') }}" required id="colorInput">
                                <span class="input-group-text" id="colorPreview" style="background-color: {{ old('color', '#6c757d') }}; min-width: 50px;">
                                    <i class="fas fa-tag text-white"></i>
                                </span>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-eye text-info me-1"></i>
                                Escolha uma cor que represente visualmente a categoria
                            </div>
                        </div>
                    </div>
                    
                    <!-- Palavras-chave -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-key text-secondary me-1"></i>Palavras-chave para Auto-categorização
                        </label>
                        <textarea name="keywords" class="form-control" rows="3" 
                                  placeholder="Ex: mercado, supermercado, feira, compras, alimentação">{{ old('keywords') }}</textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb text-warning me-1"></i>
                            <strong>Dica:</strong> Separe as palavras por vírgulas. Essas palavras serão usadas para categorizar automaticamente as transações futuras
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Exemplos por categoria:</small>
                            <div class="row mt-1">
                                <div class="col-md-4">
                                    <small class="text-primary">🍕 Alimentação:</small><br>
                                    <small class="text-muted">mercado, supermercado, restaurante, lanchonete</small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-success">🚗 Transporte:</small><br>
                                    <small class="text-muted">uber, taxi, gasolina, ônibus</small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-info">⚕️ Saúde:</small><br>
                                    <small class="text-muted">farmácia, médico, hospital, plano</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-4">
                        <div class="form-check form-switch form-check-lg">
                            <input type="checkbox" name="active" class="form-check-input" 
                                   id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="active">
                                <i class="fas fa-toggle-on text-success me-1"></i>
                                Categoria ativa
                            </label>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle text-info me-1"></i>
                            Categorias inativas não aparecem nas listas de seleção
                        </div>
                    </div>
                    
                    <!-- Preview da Categoria -->
                    <div class="alert alert-light border">
                        <h6 class="mb-2">
                            <i class="fas fa-eye text-primary me-2"></i>Preview da Categoria
                        </h6>
                        <div class="d-flex align-items-center">
                            <span class="badge me-3" id="categoryPreview" style="background-color: {{ old('color', '#6c757d') }}; font-size: 16px;">
                                <i class="fas fa-tag" id="previewIcon"></i>
                            </span>
                            <div>
                                <strong id="previewName">Nome da Categoria</strong>
                                <div class="small text-muted" id="previewType">💸 Despesa</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Categoria
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-lg" onclick="saveAndNew()">
                                <i class="fas fa-plus me-2"></i>Salvar e Nova
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Painel Lateral - Dicas e Sugestões -->
    <div class="col-lg-4">
        <!-- Dicas de Criação -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-lightbulb me-2"></i>Dicas de Criação
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0">
                        <h6 class="text-primary mb-1">
                            <i class="fas fa-tag me-2"></i>Nome da Categoria
                        </h6>
                        <small class="text-muted">
                            Use nomes específicos e claros. Ex: "Alimentação" em vez de "Comida"
                        </small>
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <h6 class="text-success mb-1">
                            <i class="fas fa-chart-line me-2"></i>Tipo Adequado
                        </h6>
                        <small class="text-muted">
                            Escolha "Despesa" para gastos, "Receita" para entradas e "Ambos" para transferências
                        </small>
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <h6 class="text-warning mb-1">
                            <i class="fas fa-key me-2"></i>Palavras-chave
                        </h6>
                        <small class="text-muted">
                            Quanto mais específicas, melhor será a categorização automática
                        </small>
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <h6 class="text-info mb-1">
                            <i class="fas fa-palette me-2"></i>Cores e Ícones
                        </h6>
                        <small class="text-muted">
                            Use cores e ícones que representem visualmente a categoria
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Categorias Sugeridas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-star me-2"></i>Categorias Populares
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                onclick="fillCategory('Alimentação', 'utensils', '#dc3545', 'expense')">
                            🍕 Alimentação
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-sm w-100"
                                onclick="fillCategory('Transporte', 'car', '#28a745', 'expense')">
                            🚗 Transporte
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info btn-sm w-100"
                                onclick="fillCategory('Saúde', 'heartbeat', '#17a2b8', 'expense')">
                            ⚕️ Saúde
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning btn-sm w-100"
                                onclick="fillCategory('Educação', 'graduation-cap', '#ffc107', 'expense')">
                            📚 Educação
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                onclick="fillCategory('Lazer', 'gamepad', '#6c757d', 'expense')">
                            🎮 Lazer
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                onclick="fillCategory('Salário', 'money-bill-wave', '#007bff', 'income')">
                            💰 Salário
                        </button>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Clique para preencher automaticamente
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Estatísticas do Sistema -->
        <div class="card shadow">
            <div class="card-header py-3 bg-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-pie me-2"></i>Estatísticas do Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-primary">
                                <i class="fas fa-tags fa-lg"></i>
                                <div class="h5 mt-1">0</div>
                                <small>Total de Categorias</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <div class="h5 mt-1">0</div>
                                <small>Categorias Ativas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ícones -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-icons me-2"></i>Escolher Ícone
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Buscar ícone...">
                </div>
                <div class="row g-2" id="iconGrid">
                    <!-- Ícones populares -->
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="home">
                            <i class="fas fa-home fa-lg"></i><br><small>home</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="car">
                            <i class="fas fa-car fa-lg"></i><br><small>car</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="utensils">
                            <i class="fas fa-utensils fa-lg"></i><br><small>utensils</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="heartbeat">
                            <i class="fas fa-heartbeat fa-lg"></i><br><small>heartbeat</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="graduation-cap">
                            <i class="fas fa-graduation-cap fa-lg"></i><br><small>graduation-cap</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="gamepad">
                            <i class="fas fa-gamepad fa-lg"></i><br><small>gamepad</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="money-bill-wave">
                            <i class="fas fa-money-bill-wave fa-lg"></i><br><small>money-bill-wave</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="shopping-cart">
                            <i class="fas fa-shopping-cart fa-lg"></i><br><small>shopping-cart</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="coffee">
                            <i class="fas fa-coffee fa-lg"></i><br><small>coffee</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="plane">
                            <i class="fas fa-plane fa-lg"></i><br><small>plane</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="building">
                            <i class="fas fa-building fa-lg"></i><br><small>building</small>
                        </button>
                    </div>
                    <div class="col-2 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-icon" data-icon="tag">
                            <i class="fas fa-tag fa-lg"></i><br><small>tag</small>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Atualizar preview em tempo real
    updatePreview();
    
    // Monitorar mudanças nos campos
    $('input[name="name"]').on('input', updatePreview);
    $('select[name="type"]').on('change', updatePreview);
    $('input[name="color"]').on('input', updateColorPreview);
    $('input[name="icon"]').on('input', updateIconPreview);
    
    // Validação em tempo real
    $('input[name="name"]').on('input', function() {
        const value = $(this).val();
        if (value.length < 2) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Modal de ícones
    $('.btn-icon').on('click', function() {
        const icon = $(this).data('icon');
        $('#iconInput').val(icon);
        updateIconPreview();
        $('#iconModal').modal('hide');
    });
    
    // Busca de ícones
    $('#iconSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('.btn-icon').each(function() {
            const icon = $(this).data('icon');
            if (icon.includes(search)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });
    
    // Auto-completar palavras-chave baseado no nome
    $('input[name="name"]').on('blur', function() {
        const name = $(this).val().toLowerCase();
        const keywordsField = $('textarea[name="keywords"]');
        
        if (keywordsField.val() === '') {
            const suggestions = getKeywordSuggestions(name);
            if (suggestions.length > 0) {
                keywordsField.val(suggestions.join(', '));
            }
        }
    });
    
    // Atalhos de teclado
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.which === 83) { // Ctrl+S
            e.preventDefault();
            $('#categoryForm').submit();
        }
    });
});

function updatePreview() {
    const name = $('input[name="name"]').val() || 'Nome da Categoria';
    const type = $('select[name="type"]').val();
    const color = $('input[name="color"]').val();
    
    $('#previewName').text(name);
    $('#categoryPreview').css('background-color', color);
    
    let typeText = '';
    switch(type) {
        case 'income':
            typeText = '💰 Receita';
            break;
        case 'expense':
            typeText = '💸 Despesa';
            break;
        case 'both':
            typeText = '🔄 Ambos';
            break;
        default:
            typeText = '💸 Despesa';
    }
    $('#previewType').text(typeText);
}

function updateColorPreview() {
    const color = $('input[name="color"]').val();
    $('#colorPreview').css('background-color', color);
    $('#categoryPreview').css('background-color', color);
}

function updateIconPreview() {
    const icon = $('input[name="icon"]').val() || 'tag';
    $('#iconPreview').removeClass().addClass('fas fa-' + icon);
    $('#previewIcon').removeClass().addClass('fas fa-' + icon);
}

function fillCategory(name, icon, color, type) {
    $('input[name="name"]').val(name);
    $('input[name="icon"]').val(icon);
    $('input[name="color"]').val(color);
    $('select[name="type"]').val(type);
    
    // Adicionar palavras-chave sugeridas
    const keywords = getKeywordSuggestions(name.toLowerCase());
    $('textarea[name="keywords"]').val(keywords.join(', '));
    
    updatePreview();
    updateColorPreview();
    updateIconPreview();
    
    // Scroll para o topo do formulário
    $('html, body').animate({
        scrollTop: $('#categoryForm').offset().top - 100
    }, 500);
}

function getKeywordSuggestions(categoryName) {
    const suggestions = {
        'alimentação': ['mercado', 'supermercado', 'restaurante', 'lanchonete', 'feira', 'padaria'],
        'transporte': ['uber', 'taxi', 'gasolina', 'ônibus', 'metrô', 'combustível'],
        'saúde': ['farmácia', 'médico', 'hospital', 'plano saúde', 'consulta', 'exame'],
        'educação': ['escola', 'faculdade', 'curso', 'livro', 'material escolar', 'mensalidade'],
        'lazer': ['cinema', 'teatro', 'parque', 'jogo', 'netflix', 'spotify'],
        'salário': ['salário', 'ordenado', 'pagamento', 'vencimento', 'remuneração'],
        'moradia': ['aluguel', 'condomínio', 'iptu', 'luz', 'água', 'gás'],
        'tecnologia': ['celular', 'computador', 'internet', 'software', 'aplicativo']
    };
    
    for (const [key, keywords] of Object.entries(suggestions)) {
        if (categoryName.includes(key)) {
            return keywords;
        }
    }
    
    return [];
}

function saveAndNew() {
    const form = $('#categoryForm');
    const input = $('<input>').attr('type', 'hidden').attr('name', 'save_and_new').val('1');
    form.append(input);
    form.submit();
}

// CSS personalizado para os botões de ícone
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .btn-icon {
            min-height: 70px;
            margin-bottom: 10px;
        }
        .btn-icon:hover {
            transform: scale(1.05);
            transition: all 0.2s;
        }
    `)
    .appendTo('head');
</script>
@endpush
