@extends('layouts.app')

@section('title', 'Relatórios')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Central de Relatórios
        </h1>
        <p class="text-muted mb-0">Análises e relatórios detalhados do seu sistema financeiro</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Relatórios</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<!-- Cards de Estatísticas Gerais -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Contas Ativas</h6>
                        <h2 class="mb-0">{{ \App\Models\BankAccount::where('active', true)->count() }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-university fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-primary-dark">
                <div class="d-flex justify-content-between">
                    <span class="small text-white-50">Contas gerenciadas</span>
                    <i class="fas fa-arrow-circle-right text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Total de Transações</h6>
                        <h2 class="mb-0">{{ number_format(\App\Models\Transaction::count()) }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-exchange-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-success-dark">
                <div class="d-flex justify-content-between">
                    <span class="small text-white-50">Movimentações registradas</span>
                    <i class="fas fa-arrow-circle-right text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Conciliações</h6>
                        <h2 class="mb-0">{{ \App\Models\Reconciliation::count() }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-balance-scale fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-info-dark">
                <div class="d-flex justify-content-between">
                    <span class="small text-white-50">Conciliações realizadas</span>
                    <i class="fas fa-arrow-circle-right text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Importações</h6>
                        <h2 class="mb-0">{{ \App\Models\StatementImport::count() }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-file-import fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-warning-dark">
                <div class="d-flex justify-content-between">
                    <span class="small text-white-50">Arquivos processados</span>
                    <i class="fas fa-arrow-circle-right text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relatórios Disponíveis -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card report-card h-100 shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exchange-alt fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Relatório de Transações</h5>
                        <small class="text-white-50">Análise detalhada das movimentações</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Visualize e exporte transações por período, conta, categoria ou status. 
                    Inclui filtros avançados e opções de exportação em múltiplos formatos.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Filtros por período, conta e categoria
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Exportação em PDF, Excel e CSV
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Gráficos interativos
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.transactions') }}" class="btn btn-primary flex-fill">
                        <i class="fas fa-chart-line me-2"></i>
                        Gerar Relatório
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/bc/reports/export/transactions/pdf" target="_blank">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/transactions/excel" target="_blank">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/transactions/csv" target="_blank">
                                <i class="fas fa-file-csv text-info me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card report-card h-100 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-balance-scale fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Relatório de Conciliações</h5>
                        <small class="text-white-50">Histórico de conciliações bancárias</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Acompanhe o histórico de conciliações realizadas, suas diferenças e 
                    identifique padrões de inconsistências.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Histórico completo de conciliações
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Análise de diferenças
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Indicadores de qualidade
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.reconciliations') }}" class="btn btn-success flex-fill">
                        <i class="fas fa-file-alt me-2"></i>
                        Gerar Relatório
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/bc/reports/export/reconciliations/pdf" target="_blank">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/reconciliations/excel" target="_blank">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/reconciliations/csv" target="_blank">
                                <i class="fas fa-file-csv text-info me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card report-card h-100 shadow-sm">
            <div class="card-header bg-gradient-info text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-tags fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Análise por Categorias</h5>
                        <small class="text-white-50">Receitas e despesas por categoria</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Analise receitas e despesas agrupadas por categoria com 
                    gráficos de pizza e comparativos temporais.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Gráficos de distribuição
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Comparação entre períodos
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Ranking de categorias
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.categories') }}" class="btn btn-info flex-fill">
                        <i class="fas fa-pie-chart me-2"></i>
                        Gerar Relatório
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/bc/reports/export/categories/pdf" target="_blank">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/categories/excel" target="_blank">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/categories/csv" target="_blank">
                                <i class="fas fa-file-csv text-info me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card report-card h-100 shadow-sm">
            <div class="card-header bg-gradient-warning text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-area fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Fluxo de Caixa</h5>
                        <small class="text-white-50">Movimentação diária e saldo acumulado</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Visualize o fluxo de caixa diário com saldo acumulado, 
                    projeções e análise de tendências.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Gráfico de linha temporal
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Projeções futuras
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        Análise de tendências
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.cash-flow') }}" class="btn btn-warning flex-fill">
                        <i class="fas fa-chart-bar me-2"></i>
                        Gerar Relatório
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/bc/reports/export/cash-flow/pdf" target="_blank">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/cash-flow/excel" target="_blank">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="/bc/reports/export/cash-flow/csv" target="_blank">
                                <i class="fas fa-file-csv text-info me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seção de Relatórios Personalizados -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-cogs text-secondary me-2"></i>
                Relatórios Personalizados
            </h5>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customReportModal">
                <i class="fas fa-plus me-1"></i>
                Criar Relatório
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="custom-report-option">
                    <div class="icon-wrapper">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </div>
                    <h6>Relatório Mensal</h6>
                    <p class="small text-muted">Resumo automático das movimentações mensais</p>
                    <button class="btn btn-sm btn-outline-primary">Configurar</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-report-option">
                    <div class="icon-wrapper">
                        <i class="fas fa-bell text-warning"></i>
                    </div>
                    <h6>Alertas Automáticos</h6>
                    <p class="small text-muted">Notificações baseadas em condições personalizadas</p>
                    <button class="btn btn-sm btn-outline-warning">Configurar</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-report-option">
                    <div class="icon-wrapper">
                        <i class="fas fa-download text-success"></i>
                    </div>
                    <h6>Exportação Agendada</h6>
                    <p class="small text-muted">Exportação automática de relatórios por email</p>
                    <button class="btn btn-sm btn-outline-success">Configurar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atalhos Rápidos -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-bolt text-warning me-2"></i>
            Ações Rápidas
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('reports.transactions') }}?period=today" class="quick-action-btn">
                    <i class="fas fa-calendar-day"></i>
                    <span>Hoje</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('reports.transactions') }}?period=week" class="quick-action-btn">
                    <i class="fas fa-calendar-week"></i>
                    <span>Esta Semana</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('reports.transactions') }}?period=month" class="quick-action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Este Mês</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('reports.transactions') }}?period=year" class="quick-action-btn">
                    <i class="fas fa-calendar"></i>
                    <span>Este Ano</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Relatório Personalizado -->
<div class="modal fade" id="customReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cogs me-2"></i>
                    Criar Relatório Personalizado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Relatório</label>
                            <input type="text" class="form-control" placeholder="Ex: Relatório Mensal Executivo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select">
                                <option>Transações</option>
                                <option>Conciliações</option>
                                <option>Categorias</option>
                                <option>Fluxo de Caixa</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Período</label>
                            <select class="form-select">
                                <option>Personalizado</option>
                                <option>Último mês</option>
                                <option>Últimos 3 meses</option>
                                <option>Último ano</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Formato</label>
                            <select class="form-select">
                                <option>PDF</option>
                                <option>Excel</option>
                                <option>CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filtros</label>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select mb-2">
                                    <option>Todas as contas</option>
                                    <option>Conta específica</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select mb-2">
                                    <option>Todas as categorias</option>
                                    <option>Categoria específica</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="scheduleReport">
                        <label class="form-check-label" for="scheduleReport">
                            Agendar execução automática
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Salvar Configuração
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.report-card {
    transition: all 0.3s ease;
    border: none;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.features-list {
    margin-top: 15px;
}

.feature-item {
    padding: 5px 0;
    font-size: 14px;
    color: var(--bs-secondary);
}

.custom-report-option {
    text-align: center;
    padding: 20px;
    border: 1px solid var(--bs-gray-200);
    border-radius: 8px;
    height: 100%;
    transition: all 0.3s ease;
}

.custom-report-option:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: var(--bs-gray-100);
    font-size: 24px;
}

.quick-action-btn {
    display: block;
    padding: 20px;
    text-align: center;
    border: 2px solid var(--bs-gray-200);
    border-radius: 8px;
    text-decoration: none;
    color: var(--bs-dark);
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    border-color: var(--bs-primary);
    background-color: var(--bs-primary);
    color: white;
    transform: translateY(-2px);
}

.quick-action-btn i {
    display: block;
    font-size: 24px;
    margin-bottom: 8px;
}

.quick-action-btn span {
    font-size: 14px;
    font-weight: 500;
}

.bg-primary-dark {
    background-color: rgba(13, 110, 253, 0.8) !important;
}

.bg-success-dark {
    background-color: rgba(25, 135, 84, 0.8) !important;
}

.bg-info-dark {
    background-color: rgba(13, 202, 240, 0.8) !important;
}

.bg-warning-dark {
    background-color: rgba(255, 193, 7, 0.8) !important;
}

@media (max-width: 768px) {
    .report-card {
        margin-bottom: 20px;
    }
    
    .quick-action-btn {
        padding: 15px;
        margin-bottom: 10px;
    }
    
    .custom-report-option {
        margin-bottom: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animação de entrada dos cards
    const reportCards = document.querySelectorAll('.report-card');
    reportCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.altKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    window.location.href = "{{ route('reports.transactions') }}";
                    break;
                case '2':
                    e.preventDefault();
                    window.location.href = "{{ route('reports.reconciliations') }}";
                    break;
                case '3':
                    e.preventDefault();
                    window.location.href = "{{ route('reports.categories') }}";
                    break;
                case '4':
                    e.preventDefault();
                    window.location.href = "{{ route('reports.cash-flow') }}";
                    break;
            }
        }
    });

    // Tooltip com atalhos
    const tooltips = [
        { element: document.querySelector('a[href*="transactions"]'), text: 'Pressione Alt+1 para acesso rápido' },
        { element: document.querySelector('a[href*="reconciliations"]'), text: 'Pressione Alt+2 para acesso rápido' },
        { element: document.querySelector('a[href*="categories"]'), text: 'Pressione Alt+3 para acesso rápido' },
        { element: document.querySelector('a[href*="cash-flow"]'), text: 'Pressione Alt+4 para acesso rápido' }
    ];

    tooltips.forEach(item => {
        if (item.element) {
            new bootstrap.Tooltip(item.element, {
                title: item.text,
                placement: 'top'
            });
        }
    });
});
</script>
@endsection
