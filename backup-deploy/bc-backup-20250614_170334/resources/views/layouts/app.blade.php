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
    
    <!-- Custom CSS -->
    <style>
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
        }
        
        /* Sidebar Responsivo */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-right: none;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 0;
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
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
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
            transition: all 0.3s ease;
            font-weight: 500;
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
        
        /* Animações */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .btn {
                font-size: 12px;
                padding: 6px 12px;
            }
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
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Scripts globais -->
    <script>
        $(document).ready(function() {
            // Navegação responsiva
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
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
                        sidebarOverlay.classList.remove('show');
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
            
            // Auto-hide alerts após 5 segundos
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
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
    </script>
    
    @stack('scripts')
</body>
</html>
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Auto-hide alerts após 5 segundos
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
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