<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Conciliação Bancária')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- jQuery para melhor compatibilidade -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --secondary-color: #6c757d;
        }
        
        /* Layout Geral */
        body {
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Responsivo */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-right: none;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: relative !important;
            width: 250px !important;
            flex: 0 0 250px;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 0 !important;
            flex: 0 0 0;
            overflow: hidden;
        }
        
        .main-content {
            flex: 1;
            min-height: 100vh;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded {
            /* Não precisa alterar margin quando usando flexbox */
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar h5 {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0;
            font-size: 1.1rem;
        }
        
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-section {
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-section-title {
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px 5px;
            margin: 0;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .page-container {
                flex-direction: column;
            }
            
            .sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                width: 250px !important;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                flex: none;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block !important;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Desktop - Layout flexbox */
        @media (min-width: 769px) {
            .page-container {
                flex-direction: row;
            }
            
            .sidebar {
                position: relative !important;
                transform: translateX(0) !important;
                display: block !important;
                flex: 0 0 250px;
            }
            
            .main-content {
                flex: 1;
            }
            
            .sidebar-toggle {
                display: none !important;
            }
        }
        
        /* Botões */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            color: white;
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
            background: transparent;
        }
        
        .btn-outline-secondary:hover {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-outline-success {
            border: 2px solid var(--success-color);
            color: var(--success-color);
            background: transparent;
        }
        
        .btn-outline-success:hover {
            background: var(--success-color);
            color: white;
        }
        
        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            background: transparent;
        }
        
        .btn-outline-danger:hover {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-outline-info {
            border: 2px solid var(--info-color);
            color: var(--info-color);
            background: transparent;
        }
        
        .btn-outline-info:hover {
            background: var(--info-color);
            color: white;
        }
        
        .btn-outline-warning {
            border: 2px solid var(--warning-color);
            color: var(--warning-color);
            background: transparent;
        }
        
        .btn-outline-warning:hover {
            background: var(--warning-color);
            color: white;
        }
        
        /* Forçar ícones */
        .fas, .far, .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
            font-weight: 900 !important;
            display: inline-block;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }
        
        /* Tabelas */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
            color: #64748b;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
        }
        
        /* Formulários */
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 10px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        /* Input Groups */
        .input-group-text {
            border: 2px solid #e2e8f0;
            border-right: none;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-weight: 500;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 1rem;
        }
        
        /* ESTILOS GLOBAIS PARA TODAS AS VIEWS - BC SISTEMA */
        /* Aplicado a: Dashboard, Configurações, Importação, Conciliação */
        
        /* Container principal de views */
        .main-content-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            min-height: 100vh !important;
            padding: 2rem 0 !important;
        }
        
        /* Cards principais */
        .bc-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .bc-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        .bc-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            border-color: #cbd5e0 !important;
        }
        
        /* Seções principais */
        .bc-section {
            background: #ffffff !important;
            border: 3px solid #e2e8f0 !important;
            border-radius: 20px !important;
            padding: 2.5rem !important;
            margin-bottom: 3rem !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .bc-section::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 6px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        .bc-section:hover {
            border-color: #cbd5e0 !important;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
            transform: translateY(-2px) !important;
        }
        
        /* Títulos padronizados */
        .bc-title {
            color: #2d3748 !important;
            font-weight: 700 !important;
            margin-bottom: 2rem !important;
            padding-bottom: 1rem !important;
            border-bottom: 3px solid #e2e8f0 !important;
            font-size: 1.4rem !important;
            position: relative !important;
        }
        
        .bc-title::after {
            content: '' !important;
            position: absolute !important;
            bottom: -3px !important;
            left: 0 !important;
            width: 80px !important;
            height: 3px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 2px !important;
        }
        
        /* Botões padronizados */
        .bc-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            color: white !important;
        }
        
        .bc-btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4) !important;
            color: white !important;
        }
        
        .bc-btn-secondary {
            border: 2px solid #6c757d !important;
            border-radius: 12px !important;
            padding: 12px 25px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        
        .bc-btn-secondary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4) !important;
        }
        
        /* Alertas padronizados */
        .bc-alert {
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
        
        .bc-alert:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        /* Tabelas padronizadas */
        .bc-table {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        }
        
        .bc-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        .bc-table th {
            border: none !important;
            padding: 1rem !important;
            font-weight: 600 !important;
        }
        
        .bc-table td {
            padding: 1rem !important;
            border-top: 1px solid #e2e8f0 !important;
            vertical-align: middle !important;
        }
        
        .bc-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05) !important;
        }
        
        /* Formulários padronizados */
        .bc-form-control {
            border: 2px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            transition: all 0.3s ease !important;
        }
        
        .bc-form-control:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
        }
        
        /* Cards de estatísticas */
        .bc-stat-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
            border: none !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
            transition: all 0.3s ease !important;
            overflow: hidden !important;
            position: relative !important;
        }
        
        .bc-stat-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        .bc-stat-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        
        /* Animações globais */
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
        
        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Aplicar animação fadeInUp a elementos principais */
        .bc-card, .bc-section, .bc-stat-card {
            animation: fadeInUp 0.6s ease-out !important;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .bc-section {
                padding: 1.5rem !important;
                margin-bottom: 2rem !important;
            }
            
            .bc-card {
                margin-bottom: 1rem !important;
            }
            
            .bc-title {
                font-size: 1.2rem !important;
            }
        }
        
        /* Classes de cores personalizadas para garantir visibilidade */
        .text-gray-800 {
            color: #2d3748 !important;
        }
        
        .text-gray-600 {
            color: #4a5568 !important;
        }
        
        .text-gray-500 {
            color: #6b7280 !important;
        }
        
        .text-gray-300 {
            color: #d1d5db !important;
        }
        
        .font-weight-bold {
            font-weight: 700 !important;
        }
        
        /* Correções de contraste para melhor visibilidade */
        .card-header h6,
        .card-header .font-weight-bold {
            color: #2d3748 !important;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9) !important;
        }
        
        .sidebar .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-section-title {
            color: rgba(255,255,255,0.7) !important;
        }
        
        /* Melhorar contraste dos ícones */
        .fa-2x.text-gray-300 {
            color: #9ca3af !important;
        }
        
        /* Títulos principais */
        .h3.text-gray-800,
        h1.text-gray-800,
        h2.text-gray-800,
        h3.text-gray-800 {
            color: #1f2937 !important;
        }
        
        /* Cards com bordas coloridas */
        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }
        
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        
        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }
        
        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }
        
        /* Melhorar legibilidade de badges */
        .badge {
            font-weight: 600;
        }
        
        /* Garantir que textos em cards sejam legíveis */
        .card-body {
            color: #2d3748;
        }
        
        .card-title {
            color: #1f2937 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Container Principal -->
    <div class="page-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5>
                    <i class="fas fa-chart-line me-2"></i>
                    BCSystem
                </h5>
            </div>
            
            <div class="nav-sections">
                <!-- Seção Principal -->
                <div class="nav-section">
                    <h6 class="nav-section-title">Principal</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Seção Financeira -->
                <div class="nav-section">
                    <h6 class="nav-section-title">Financeiro</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('account-management.*') ? 'active' : '' }}" href="{{ route('account-management.index') }}">
                                <i class="fas fa-chart-line"></i>
                                <span>Gestão de Contas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}" href="{{ route('bank-accounts.index') }}">
                                <i class="fas fa-university"></i>
                                <span>Contas Bancárias</span>
                            </a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transações</span>
                            @if(isset($pendingTransactions) && $pendingTransactions > 0)
                                <span class="nav-badge">{{ $pendingTransactions }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reconciliations.*') ? 'active' : '' }}" href="{{ route('reconciliations.index') }}">
                            <i class="fas fa-balance-scale"></i>
                            <span>Conciliações</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.financial-management') ? 'active' : '' }}" href="{{ route('reports.financial-management') }}">
                            <i class="fas fa-coins"></i>
                            <span>Gestão Financeira</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Seção Contas a Pagar/Receber -->
            <div class="nav-section">
                <h6 class="nav-section-title">Contas a Pagar/Receber</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                            <i class="fas fa-truck"></i>
                            <span>Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('account-payables.*') ? 'active' : '' }}" href="{{ route('account-payables.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Contas a Pagar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('account-receivables.*') ? 'active' : '' }}" href="{{ route('account-receivables.index') }}">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Contas a Receber</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Seção Gestão -->
            <div class="nav-section">
                <h6 class="nav-section-title">Gestão</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="fas fa-tags"></i>
                            <span>Categorias</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('imports.*') ? 'active' : '' }}" href="{{ route('imports.index') }}">
                            <i class="fas fa-file-import"></i>
                            <span>Importações</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Seção Relatórios -->
            <div class="nav-section">
                <h6 class="nav-section-title">Relatórios</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Seção Sistema -->
            <div class="nav-section">
                <h6 class="nav-section-title">Sistema</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                            <i class="fas fa-cogs"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container-fluid p-4">
            <!-- Header -->
            @hasSection('header')
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        @yield('header')
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('header-actions')
                    </div>
                </div>
            @endif

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Corrija os erros abaixo:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Content -->
            @yield('content')
        </div>
    </div>
    
    </div> <!-- Fecha page-container -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Scripts globais -->
    <script>
        $(document).ready(function() {
            console.log('Sistema de navegação iniciado - Menu scroll junto');
            
            // Navegação responsiva para mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    console.log('Toggle clicado');
                    sidebar.classList.toggle('show');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('show');
                    }
                });
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    console.log('Overlay clicado');
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Fechar sidebar ao clicar em link no mobile
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        if (sidebarOverlay) {
                            sidebarOverlay.classList.remove('show');
                        }
                    }
                });
            });
            
            // Inicializar tooltips globalmente
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Inicializar popovers globalmente
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Auto-hide alerts após 8 segundos (tempo aumentado)
            setTimeout(function() {
                $('.alert.alert-dismissible').each(function() {
                    $(this).fadeOut('slow');
                });
            }, 8000);
            
            // Pausa o timer quando o mouse está sobre o alerta
            $('.alert.alert-dismissible').hover(
                function() {
                    // Mouse enter - parar o timer
                    $(this).data('timer-paused', true);
                },
                function() {
                    // Mouse leave - reiniciar o timer
                    const $alert = $(this);
                    $alert.data('timer-paused', false);
                    setTimeout(function() {
                        if (!$alert.data('timer-paused')) {
                            $alert.fadeOut('slow');
                        }
                    }, 3000);
                }
            );
        });
        
        // Função para formatar valores monetários
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }
        
        // Função para formatar datas
        function formatDate(date) {
            return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
        }
        
        // Adicionar animação aos cards
        $('.card').addClass('fade-in-up');
        
        // Confirmar ações perigosas
        $('[data-confirm]').click(function(e) {
            if (!confirm($(this).data('confirm'))) {
                e.preventDefault();
            }
        });
        
        // Loading nos botões de submit
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        });
        
        // Função para copiar texto
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Copiado para a área de transferência!', 'success');
            });
        }
        
        // Função para mostrar toast
        function showToast(message, type = 'info') {
            const toast = `
                <div class="toast align-items-center text-bg-${type} border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            $('body').append(toast);
            $('.toast').last().toast('show');
            
            // Remove toast após ser escondido
            $('.toast').last().on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>