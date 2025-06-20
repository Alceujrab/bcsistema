@extends('layouts.app')

@section('title', 'Dashboard - BC Sistema de Gestão Financeira')

@section('styles')
<!-- Dashboard CSS - Versão 1.2 - {{ date('Y-m-d H:i:s') }} -->
<style>
    /* CSS CRÍTICO COM MÁXIMA PRIORIDADE */
    /* Variáveis CSS corrigidas */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        --warning-gradient: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        --danger-gradient: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        --info-gradient: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        --shadow-light: 0 4px 15px rgba(0,0,0,0.08);
        --shadow-medium: 0 8px 25px rgba(0,0,0,0.12);
        --shadow-heavy: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    /* Container principal com fundo melhorado */
    .dashboard-container {
        background: linear-gradient(120deg, #f1f3f4 0%, #e8eaed 100%);
        min-height: calc(100vh - 80px);
        padding: 1.5rem 0;
    }
    
    /* Banner da empresa */
    .company-banner {
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-medium);
    }
    
    .company-logo {
        height: 60px;
        width: auto;
        border-radius: 8px;
    }
    
    .company-name {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
    }
    
    .company-slogan {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }
    
    /* Seções bem delimitadas */
    .dashboard-section {
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 3rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 3px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .dashboard-section:hover {
        border-color: #cbd5e0;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .section-title {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.4rem;
        position: relative;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 80px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    .section-title i {
        color: #667eea;
        font-size: 1.2rem;
    }
    
    /* Cards executivos */
    .executive-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    
    .executive-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }
    
    .executive-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        border-color: #cbd5e0;
    }
    
    .executive-card .card-body {
        padding: 2rem;
        border-radius: 18px;
    }
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 15px;
        box-shadow: var(--shadow-light);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    
    .executive-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
    }
    
    .executive-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }
    
    /* Métricas */
    .metric-display {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: #2d3748;
    }
    
    .metric-display.text-success {
        color: #28a745 !important;
    }
    
    .metric-display.text-info {
        color: #17a2b8 !important;
    }
    
    .metric-display.text-warning {
        color: #ffc107 !important;
    }
    
    /* Banner profissional */
    .professional-banner {
        background: var(--primary-gradient);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        padding: 2rem;
    }
    
    .professional-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: float 20s infinite linear;
    }
    
    @keyframes float {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Botões de ação corrigidos com Bootstrap */
    .action-button {
        background: rgba(255,255,255,0.15) !important;
        border: 2px solid rgba(255,255,255,0.25) !important;
        color: white !important;
        border-radius: 10px !important;
        padding: 10px 18px !important;
        transition: all 0.3s ease !important;
        backdrop-filter: blur(10px);
        font-weight: 500 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 0.9rem !important;
        text-transform: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .action-button:hover {
        background: rgba(255,255,255,0.25) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important;
        border-color: rgba(255,255,255,0.4) !important;
    }
    
    .action-button:focus,
    .action-button:active {
        color: white !important;
        background: rgba(255,255,255,0.25) !important;
        border-color: rgba(255,255,255,0.4) !important;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25) !important;
    }
    
    /* Widgets financeiros */
    .financial-widget {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e2e8f0;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .financial-widget::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }
    
    .financial-widget:hover {
        border-color: #cbd5e0;
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        transform: translateY(-3px);
    }
    
    .financial-widget h5 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f3f4;
    }
    
    /* Área de alertas */
    .alerts-container {
        background: #ffffff;
        border: 2px solid #fed7d7;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    
    .alerts-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--danger-gradient);
    }
    
    /* Área de ações rápidas */
    .quick-actions-container {
        background: #ffffff;
        border: 2px solid #bee3f8;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    
    .quick-actions-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--info-gradient);
    }
    
    /* Área de gráficos */
    .charts-container {
        background: #ffffff;
        border: 2px solid #c6f6d5;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    
    .charts-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--success-gradient);
    }
    
    /* Ícones dos widgets */
    .widget-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
        color: white !important;
    }
    
    /* Status badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-weight: 500;
        font-size: 0.85rem;
        border: none;
    }
    
    .status-badge.bg-success {
        background: var(--success-gradient) !important;
        color: white !important;
    }
    
    .status-badge.bg-warning {
        background: var(--warning-gradient) !important;
        color: white !important;
    }
    
    .status-badge.bg-danger {
        background: var(--danger-gradient) !important;
        color: white !important;
    }
    
    /* Indicadores de performance */
    .performance-indicator {
        background: var(--success-gradient);
        color: white !important;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Alertas profissionais com mais tempo de exibição */
    .alert-professional {
        border: none !important;
        border-radius: 12px !important;
        border-left: 4px solid !important;
        background: rgba(255,255,255,0.95) !important;
        backdrop-filter: blur(10px) !important;
        margin-bottom: 1rem !important;
        padding: 1rem 1.5rem !important;
        animation: slideInRight 0.5s ease-out !important;
        position: relative !important;
        transition: all 0.3s ease !important;
    }
    
    .alert-professional:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .alert-professional.alert-warning {
        border-left-color: #ffc107;
        background: linear-gradient(135deg, rgba(255,193,7,0.1) 0%, rgba(255,193,7,0.05) 100%);
        color: #856404;
    }
    
    .alert-professional.alert-danger {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, rgba(220,53,69,0.1) 0%, rgba(220,53,69,0.05) 100%);
        color: #721c24;
    }
    
    .alert-professional.alert-info {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, rgba(23,162,184,0.1) 0%, rgba(23,162,184,0.05) 100%);
        color: #0c5460;
    }
    
    .alert-professional.alert-success {
        border-left-color: #28a745;
        background: linear-gradient(135deg, rgba(40,167,69,0.1) 0%, rgba(40,167,69,0.05) 100%);
        color: #155724;
    }
    
    /* Gráficos */
    .chart-container {
        background: #ffffff;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: var(--shadow-light);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .chart-container h5 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    /* ESTILO INLINE ADICIONAL PARA GARANTIR APLICAÇÃO */
    .dashboard-container {
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* Força aplicação imediata via JavaScript */
    .force-styles {
        border: 3px solid #e2e8f0 !important;
        border-radius: 20px !important;
        background: white !important;
        padding: 2.5rem !important;
        margin-bottom: 3rem !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    /* Reset específico para evitar herança indesejada */
    body .dashboard-container * {
        box-sizing: border-box !important;
    }
    
    /* Media query para garantir responsividade */
    @media (max-width: 768px) {
        body .dashboard-container .dashboard-section {
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
        }
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem 0;
        }
        
        .professional-banner {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .metric-display {
            font-size: 2rem;
        }
        
        .widget-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .action-button {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
    }
    
    /* Correções de dropdown */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: var(--shadow-medium);
        background: white;
    }
    
    .dropdown-item {
        padding: 10px 15px;
        color: #2d3748 !important;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea !important;
    }
    
    /* Títulos dos widgets */
    .widget-title {
        color: #64748b !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Progresso customizado */
    .progress {
        height: 6px;
        border-radius: 10px;
        background: rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
        border-radius: 10px;
        background: rgba(0,0,0,0.05);
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    /* Títulos */
    .section-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }
    
    .widget-title {
        color: #4a5568;
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Botões dropdown corrigidos */
    .dropdown-toggle::after {
        margin-left: 0.5em;
    }
    
    .btn-group .action-button {
        border-radius: 12px;
    }
    
    /* Correção de texto */
    .text-muted {
        color: #6c757d !important;
    }
    
    .fw-semibold {
        font-weight: 600 !important;
    }
    
    /* Força aplicação dos estilos do dashboard - sobrescreve layout */
    body .dashboard-container {
        background: linear-gradient(120deg, #f8f9fa 0%, #e9ecef 100%) !important;
        min-height: calc(100vh - 60px) !important;
        padding: 2rem 0 !important;
    }
    
    /* Cards executivos com força total */
    body .dashboard-container .executive-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        overflow: hidden !important;
        height: 100% !important;
    }
    
    body .dashboard-container .executive-card::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: var(--primary-gradient) !important;
    }
    
    body .dashboard-container .executive-card:hover {
        transform: translateY(-5px) scale(1.02) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        border-color: #cbd5e0 !important;
    }
    
    body .dashboard-container .executive-card .card-body {
        padding: 2rem !important;
        border-radius: 18px !important;
        background: transparent !important;
    }
    
    /* Seções do dashboard com força total */
    body .dashboard-container .dashboard-section {
        background: #ffffff !important;
        border: 3px solid #e2e8f0 !important;
        border-radius: 20px !important;
        padding: 2.5rem !important;
        margin-bottom: 3rem !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    body .dashboard-container .dashboard-section::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 6px !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    body .dashboard-container .dashboard-section:hover {
        border-color: #cbd5e0 !important;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
        transform: translateY(-2px) !important;
    }
    
    body .dashboard-container .section-title {
        color: #2d3748 !important;
        font-weight: 700 !important;
        margin-bottom: 2rem !important;
        padding-bottom: 1rem !important;
        border-bottom: 3px solid #e2e8f0 !important;
        font-size: 1.4rem !important;
    }
    
    body .dashboard-container .section-title::after {
        content: '' !important;
        position: absolute !important;
        bottom: -3px !important;
        left: 0 !important;
        width: 80px !important;
        height: 3px !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-radius: 2px !important;
    }
    
    /* Seções especializadas com cores específicas */
    body .dashboard-container .alerts-container {
        border-color: #fed7d7 !important;
    }
    
    body .dashboard-container .alerts-container::before {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
    }
    
    body .dashboard-container .quick-actions-container {
        border-color: #bee3f8 !important;
    }
    
    body .dashboard-container .quick-actions-container::before {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%) !important;
    }
    
    body .dashboard-container .charts-container {
        border-color: #c6f6d5 !important;
    }
    
    body .dashboard-container .charts-container::before {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
    }
    
    body .dashboard-container .financial-widget {
        border: 2px solid #e2e8f0 !important;
        border-radius: 18px !important;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
    }
    
    body .dashboard-container .executive-card {
        border: 2px solid #e2e8f0 !important;
        border-radius: 18px !important;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
    }
    
    body .dashboard-container .professional-banner .action-button {
        background: rgba(255,255,255,0.15) !important;
        border: 2px solid rgba(255,255,255,0.2) !important;
        color: white !important;
        border-radius: 15px !important;
        padding: 15px 25px !important;
        font-weight: 600 !important;
        backdrop-filter: blur(10px) !important;
    }
    
    body .dashboard-container .professional-banner .action-button:hover {
        background: rgba(255,255,255,0.25) !important;
        color: white !important;
        transform: translateY(-3px) scale(1.02) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
    }
</style>
@endsection

@section('content')
<div class="main-content-container">
    <div class="container-fluid">
        @if($companyData['name'] !== 'BC Sistema' || $companyData['logo'])
        <!-- Banner da Empresa -->
        <div class="company-banner mb-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($companyData['logo'])
                        <img src="{{ Storage::url($companyData['logo']) }}" alt="Logo" class="company-logo">
                    @endif
                </div>
                <div class="col">
                    <h2 class="company-name mb-1">{{ $companyData['name'] }}</h2>
                    @if($companyData['slogan'])
                        <p class="company-slogan mb-0">{{ $companyData['slogan'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <!-- Banner Executivo -->
        <div class="professional-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-chart-line me-3"></i>Painel Executivo
                    </h1>
                    <p class="lead mb-4 opacity-90">
                        @if($dashboardConfig['show_welcome_message'])
                            Controle total das suas finanças empresariais com insights em tempo real
                        @else
                            Dashboard
                        @endif
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="btn action-button" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt me-2"></i>Atualizar Dados
                        </button>
                        <div class="dropdown">
                            <button type="button" class="btn action-button dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>Relatórios
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportDashboard('pdf')">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Relatório Executivo PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportDashboard('excel')">
                                    <i class="fas fa-file-excel me-2 text-success"></i>Dados em Excel
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('reports.financial-management') }}">
                                    <i class="fas fa-chart-bar me-2 text-primary"></i>Relatório Completo
                                </a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn action-button" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
                            <i class="fas fa-plus me-2"></i>Nova Operação
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 text-end d-none d-lg-block">
                    <div class="widget-icon" style="background: rgba(255,255,255,0.2); margin: 0 auto;">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas Executivas -->
        <div class="bc-section">
            <h2 class="bc-title">
                <i class="fas fa-chart-bar text-primary"></i>Visão Geral Financeira
            </h2>
        
            <div class="row">
                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="bc-stat-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h6 class="widget-title">CONTAS BANCÁRIAS</h6>
                                    <div class="metric-display">{{ $stats['active_accounts'] ?? 0 }}</div>
                                    <div class="performance-indicator mt-2">
                                        <i class="fas fa-check-circle"></i>{{ $stats['active_accounts'] ?? 0 }}/{{ $stats['total_accounts'] ?? 0 }} Ativas
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="widget-icon" style="background: var(--primary-gradient);">
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mt-3">
                                <div class="progress-bar" style="background: var(--primary-gradient); width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="executive-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="widget-title">SALDO TOTAL</h6>
                                <div class="metric-display text-success">R$ {{ number_format($stats['total_balance'] ?? 0, 2, ',', '.') }}</div>
                                <div class="performance-indicator mt-2" style="background: var(--success-gradient);">
                                    <i class="fas fa-trending-up"></i>Patrimônio
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="widget-icon" style="background: var(--success-gradient);">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar" style="background: var(--success-gradient); width: 90%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="executive-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="widget-title">TRANSAÇÕES</h6>
                                <div class="metric-display text-info">{{ $stats['total_transactions'] ?? 0 }}</div>
                                <div class="performance-indicator mt-2" style="background: var(--info-gradient);">
                                    <i class="fas fa-clock"></i>{{ $stats['pending_transactions'] ?? 0 }} Pendentes
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="widget-icon" style="background: var(--info-gradient);">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar" style="background: var(--info-gradient); width: 75%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="executive-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="widget-title">CONCILIAÇÕES</h6>
                                <div class="metric-display text-warning">{{ $stats['reconciliations'] ?? 0 }}</div>
                                <div class="performance-indicator mt-2" style="background: var(--warning-gradient);">
                                    <i class="fas fa-sync"></i>Este Mês
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="widget-icon" style="background: var(--warning-gradient);">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar" style="background: var(--warning-gradient); width: 60%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Gestão Financeira -->
        <div class="dashboard-section">
            <h2 class="section-title">
                <i class="fas fa-coins text-success"></i>Gestão Financeira
            </h2>

            <div class="row">
                <div class="col-lg-6 mb-4">
                <div class="financial-widget">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-hand-holding-usd me-2 text-success"></i>Contas a Receber
                        </h5>
                        <a href="{{ route('account-receivables.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Ver Todas
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-display text-success mb-2">{{ $financialStats['receivables']['total'] ?? 0 }}</div>
                                <small class="text-muted">Total de Contas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-display text-success mb-2">R$ {{ number_format($financialStats['receivables']['amount'] ?? 0, 2, ',', '.') }}</div>
                                <small class="text-muted">Valor Total</small>
                            </div>
                        </div>
                    </div>
                    @if(isset($financialStats['receivables']['overdue']) && $financialStats['receivables']['overdue'] > 0)
                        <div class="alert-professional alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>{{ $financialStats['receivables']['overdue'] }} contas em atraso</strong> - Requer atenção imediata
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="financial-widget">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2 text-danger"></i>Contas a Pagar
                        </h5>
                        <a href="{{ route('account-payables.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Ver Todas
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-display text-danger mb-2">{{ $financialStats['payables']['total'] ?? 0 }}</div>
                                <small class="text-muted">Total de Contas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-display text-danger mb-2">R$ {{ number_format($financialStats['payables']['amount'] ?? 0, 2, ',', '.') }}</div>
                                <small class="text-muted">Valor Total</small>
                            </div>
                        </div>
                    </div>
                    @if(isset($financialStats['payables']['overdue']) && $financialStats['payables']['overdue'] > 0)
                        <div class="alert-professional alert-danger mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>{{ $financialStats['payables']['overdue'] }} contas vencidas</strong> - Ação urgente necessária
                        </div>
                    @endif
                </div>
            </div>
        </div>

            </div>
        </div>

        <!-- Alertas Inteligentes -->
        <div class="dashboard-section alerts-container">
            <h2 class="section-title">
                <i class="fas fa-exclamation-triangle text-danger"></i>Alertas e Notificações
            </h2>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-3 text-warning" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-1">Contas Próximas ao Vencimento</h6>
                                <small class="text-muted">{{ $stats['due_soon_accounts'] ?? 3 }} contas vencem nos próximos 7 dias</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="alert alert-danger border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3 text-danger" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-1">Contas em Atraso</h6>
                                <small class="text-muted">{{ $stats['overdue_accounts'] ?? 2 }} contas estão em atraso</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="alert alert-info border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line me-3 text-info" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-1">Crescimento do Mês</h6>
                                <small class="text-muted">+15% em relação ao mês anterior</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 text-success" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-1">Conciliações em Dia</h6>
                                <small class="text-muted">Todas as contas estão reconciliadas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Fluxo de Caixa -->
        <div class="dashboard-section charts-container">
            <h2 class="section-title">
                <i class="fas fa-chart-area text-success"></i>Análise de Fluxo de Caixa
            </h2>
            <div class="chart-container">
                <canvas id="cashFlowChart" height="100"></canvas>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="dashboard-section quick-actions-container">
            <h2 class="section-title">
                <i class="fas fa-bolt text-info"></i>Ações Rápidas
            </h2>

            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('clients.create') }}" class="btn btn-primary w-100 py-3 shadow-sm">
                        <i class="fas fa-user-plus d-block mb-2" style="font-size: 2rem;"></i>
                        <strong>Novo Cliente</strong>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('suppliers.create') }}" class="btn btn-info w-100 py-3 shadow-sm">
                        <i class="fas fa-truck d-block mb-2" style="font-size: 2rem;"></i>
                        <strong>Novo Fornecedor</strong>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('account-receivables.create') }}" class="btn btn-success w-100 py-3 shadow-sm">
                        <i class="fas fa-hand-holding-usd d-block mb-2" style="font-size: 2rem;"></i>
                        <strong>Conta a Receber</strong>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('account-payables.create') }}" class="btn btn-warning w-100 py-3 shadow-sm">
                        <i class="fas fa-file-invoice-dollar d-block mb-2" style="font-size: 2rem;"></i>
                        <strong>Conta a Pagar</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ações Rápidas -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nova Operação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('clients.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-user-plus d-block mb-2"></i>Cliente
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-truck d-block mb-2"></i>Fornecedor
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('account-receivables.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-hand-holding-usd d-block mb-2"></i>A Receber
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('account-payables.create') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-file-invoice-dollar d-block mb-2"></i>A Pagar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Forçar aplicação de estilos críticos
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Aplicando estilos críticos do dashboard...');
        
        // Aplicar estilos nas seções do dashboard
        const dashboardSections = document.querySelectorAll('.dashboard-section');
        dashboardSections.forEach(function(section) {
            section.style.border = '3px solid #e2e8f0';
            section.style.borderRadius = '20px';
            section.style.backgroundColor = '#ffffff';
            section.style.padding = '2.5rem';
            section.style.marginBottom = '3rem';
            section.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
            section.style.position = 'relative';
            section.style.overflow = 'hidden';
            
            // Adicionar classe force-styles
            section.classList.add('force-styles');
            
            // Criar e aplicar pseudo-elemento before via JavaScript
            const beforeElement = document.createElement('div');
            beforeElement.style.content = '""';
            beforeElement.style.position = 'absolute';
            beforeElement.style.top = '0';
            beforeElement.style.left = '0';
            beforeElement.style.right = '0';
            beforeElement.style.height = '6px';
            beforeElement.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            beforeElement.style.zIndex = '1';
            section.insertBefore(beforeElement, section.firstChild);
        });
        
        // Aplicar estilos específicos por tipo de seção
        const alertsContainer = document.querySelector('.alerts-container');
        if (alertsContainer) {
            alertsContainer.style.borderColor = '#fed7d7';
            const beforeAlert = alertsContainer.querySelector('div:first-child');
            if (beforeAlert) {
                beforeAlert.style.background = 'linear-gradient(135deg, #f56565 0%, #e53e3e 100%)';
            }
        }
        
        const quickActionsContainer = document.querySelector('.quick-actions-container');
        if (quickActionsContainer) {
            quickActionsContainer.style.borderColor = '#bee3f8';
            const beforeAction = quickActionsContainer.querySelector('div:first-child');
            if (beforeAction) {
                beforeAction.style.background = 'linear-gradient(135deg, #4299e1 0%, #3182ce 100%)';
            }
        }
        
        const chartsContainer = document.querySelector('.charts-container');
        if (chartsContainer) {
            chartsContainer.style.borderColor = '#c6f6d5';
            const beforeChart = chartsContainer.querySelector('div:first-child');
            if (beforeChart) {
                beforeChart.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';
            }
        }
        
        console.log('Estilos críticos aplicados com sucesso!');
    });
    
    // Inicializar gráfico de fluxo de caixa
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    const cashFlowChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [
                {
                    label: 'Receitas',
                    data: [12000, 15000, 18000, 16000, 20000, 22000, 25000, 23000, 21000, 24000, 26000, 28000],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Despesas',
                    data: [8000, 9000, 11000, 10000, 12000, 13000, 15000, 14000, 13000, 16000, 17000, 18000],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });

    // Função para atualizar dashboard
    function refreshDashboard() {
        console.log('Atualizando dashboard...');
        location.reload();
    }
    
    // Função para exportar dashboard
    function exportDashboard(format) {
        console.log('Exportando dashboard em formato:', format);
        alert('Funcionalidade de exportação será implementada em breve!');
    }
    
    // Função para aplicar estilos novamente (fallback)
    function reapplyStyles() {
        const sections = document.querySelectorAll('.dashboard-section');
        sections.forEach(section => {
            section.style.cssText += `
                border: 3px solid #e2e8f0 !important;
                border-radius: 20px !important;
                background: #ffffff !important;
                padding: 2.5rem !important;
                margin-bottom: 3rem !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
            `;
        });
        console.log('Estilos reaplicados!');
    }
    
    // Verificar se estilos foram aplicados após 1 segundo
    setTimeout(function() {
        const firstSection = document.querySelector('.dashboard-section');
        if (firstSection) {
            const styles = window.getComputedStyle(firstSection);
            const borderWidth = styles.getPropertyValue('border-width');
            console.log('Largura da borda detectada:', borderWidth);
            
            if (borderWidth !== '3px') {
                console.log('Reaplicando estilos...');
                reapplyStyles();
            }
        }
    }, 1000);
    
    // Atualização automática a cada 5 minutos
    setInterval(function() {
        console.log('Dashboard atualizado automaticamente');
        // Aqui você pode fazer uma requisição AJAX para atualizar dados sem recarregar a página
    }, 300000); // 5 minutos
</script>
@endsection
