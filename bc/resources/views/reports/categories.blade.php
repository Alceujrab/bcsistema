@extends('layouts.app')

@section('title', 'Relatório de Análise por Categorias')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-chart-pie text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Análise por Categorias</h2>
        <p class="text-muted mb-0">Análise detalhada das movimentações financeiras por categoria</p>
    </div>
</div>
                            @php
                                $creditData = $types->firstWhere('type', 'credit');
                                $debitData = $types->firstWhere('type', 'debit');
                                $totalCredit = floatval($creditData->total ?? 0);
                                $totalDebit = floatval($debitData->total ?? 0);
                                $balance = $totalCredit - $totalDebit;
                                $creditCount = intval($creditData->count ?? 0);
                                $debitCount = intval($debitData->count ?? 0);
                                $totalTransactions = $creditCount + $debitCount;
                            @endphpn

@section('header-actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-success" onclick="exportData('excel')">
        <i class="fas fa-file-excel me-2"></i>Excel
    </button>
    <button class="btn btn-outline-danger" onclick="exportData('pdf')">
        <i class="fas fa-file-pdf me-2"></i>PDF
    </button>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimir
    </button>
</div>
@endsection

@section('content')
<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filtros de Período
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.categories') }}" id="categoryFilters">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-alt text-success me-2"></i>Data Inicial
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-lg" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-check text-info me-2"></i>Data Final
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-lg" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-exchange-alt text-warning me-2"></i>Tipo de Transação
                    </label>
                    <select name="transaction_type" class="form-select form-select-lg">
                        <option value="">Todos os tipos</option>
                        <option value="credit" {{ request('transaction_type') == 'credit' ? 'selected' : '' }}>
                            Apenas Créditos
                        </option>
                        <option value="debit" {{ request('transaction_type') == 'debit' ? 'selected' : '' }}>
                            Apenas Débitos
                        </option>
                    </select>
                </div>
                
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('month')">
                                Este Mês
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('quarter')">
                                Trimestre
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('year')">
                                Este Ano
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickPeriod('last12')">
                                Últimos 12 Meses
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.categories') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Limpar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-chart-pie me-2"></i>Gerar Análise
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if($categorySummary->count() > 0)
<!-- Cards de Resumo Geral -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-tags fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted">Total de Categorias</h6>
                <h2 class="text-primary mb-0">{{ $categorySummary->count() }}</h2>
                <small class="text-muted">Categorizadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-arrow-up fa-2x text-success"></i>
                </div>
                <h6 class="text-muted">Total de Créditos</h6>
                @php
                    $totalCredits = $categorySummary->flatten()->where('type', 'credit')->sum('total');
                @endphp
                <h2 class="text-success mb-0">R$ {{ number_format($totalCredits, 2, ',', '.') }}</h2>
                <small class="text-muted">No período</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-arrow-down fa-2x text-danger"></i>
                </div>
                <h6 class="text-muted">Total de Débitos</h6>
                @php
                    $totalDebits = $categorySummary->flatten()->where('type', 'debit')->sum('total');
                @endphp
                <h2 class="text-danger mb-0">R$ {{ number_format($totalDebits, 2, ',', '.') }}</h2>
                <small class="text-muted">No período</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-balance-scale fa-2x text-info"></i>
                </div>
                <h6 class="text-muted">Saldo Líquido</h6>
                @php
                    $netBalance = $totalCredits - $totalDebits;
                @endphp
                <h2 class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                    R$ {{ number_format($netBalance, 2, ',', '.') }}
                </h2>
                <small class="text-muted">Resultado</small>
            </div>
        </div>
    </div>
</div>

<!-- Controles de Visualização -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Visualização dos Dados</h5>
                <small class="text-muted">Escolha como visualizar as informações</small>
            </div>
            <div class="d-flex gap-2">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" onclick="toggleView('cards')" id="cardsBtn">
                        <i class="fas fa-th-large me-2"></i>Cards
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="toggleView('table')" id="tableBtn">
                        <i class="fas fa-table me-2"></i>Tabela
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="toggleView('chart')" id="chartBtn">
                        <i class="fas fa-chart-pie me-2"></i>Gráfico
                    </button>
                </div>
                <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#sortOptions">
                    <i class="fas fa-sort me-2"></i>Ordenar
                </button>
            </div>
        </div>
        
        <div class="collapse mt-3" id="sortOptions">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="sortBy">
                        <option value="name">Nome da Categoria</option>
                        <option value="credits">Total de Créditos</option>
                        <option value="debits">Total de Débitos</option>
                        <option value="balance">Saldo Líquido</option>
                        <option value="transactions">Número de Transações</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="sortOrder">
                        <option value="desc">Decrescente</option>
                        <option value="asc">Crescente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="searchCategories" 
                           placeholder="Buscar categorias...">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="filterBalance">
                        <option value="">Todos</option>
                        <option value="positive">Saldo Positivo</option>
                        <option value="negative">Saldo Negativo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-secondary w-100" onclick="clearFilters()">
                        Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visualização em Cards -->
<div id="cardsView">
    <div class="row" id="categoriesContainer">
        @foreach($categorySummary as $category => $types)
        <div class="col-lg-4 col-md-6 mb-4 category-card" 
             data-category="{{ strtolower($category ?: 'sem categoria') }}">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-tag me-2"></i>{{ $category ?: 'Sem categoria' }}
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light btn-outline-light dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="viewCategoryDetails('{{ $category }}')">
                                        <i class="fas fa-eye me-2"></i>Ver Detalhes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="exportCategory('{{ $category }}')">
                                        <i class="fas fa-download me-2"></i>Exportar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $creditData = $types->firstWhere('type', 'credit');
                        $debitData = $types->firstWhere('type', 'debit');
                        $totalCredit = floatval($creditData->total ?? 0);
                        $totalDebit = floatval($debitData->total ?? 0);
                        $balance = $totalCredit - $totalDebit;
                        $creditCount = intval($creditData->count ?? 0);
                        $debitCount = intval($debitData->count ?? 0);
                        $totalTransactions = $creditCount + $debitCount;
                    @endphp
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fas fa-arrow-up fa-2x text-success"></i>
                                </div>
                                <h6 class="text-muted small">CRÉDITOS</h6>
                                <h4 class="text-success mb-1">R$ {{ number_format($totalCredit, 2, ',', '.') }}</h4>
                                <small class="text-muted">{{ $creditCount }} transações</small>
                            </div>
                        </div>
                        
                        <div class="col-6 border-start">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fas fa-arrow-down fa-2x text-danger"></i>
                                </div>
                                <h6 class="text-muted small">DÉBITOS</h6>
                                <h4 class="text-danger mb-1">R$ {{ number_format($totalDebit, 2, ',', '.') }}</h4>
                                <small class="text-muted">{{ $debitCount }} transações</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="text-center">
                        <h6 class="text-muted mb-2">SALDO LÍQUIDO</h6>
                        <h3 class="mb-2 {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-{{ $balance >= 0 ? 'plus' : 'minus' }}-circle me-1"></i>
                            R$ {{ number_format(abs($balance), 2, ',', '.') }}
                        </h3>
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <small class="text-muted">Total de Transações</small>
                                <br>
                                <span class="badge bg-primary fs-6">{{ $totalTransactions }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Média por Transação</small>
                                <br>
                                <span class="badge bg-secondary fs-6">
                                    R$ {{ number_format($totalTransactions > 0 ? ($totalCredit + $totalDebit) / $totalTransactions : 0, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barra de Progresso -->
                    @if($totalCredits > 0 || $totalDebits > 0)
                    <div class="mt-3">
                        <small class="text-muted">Participação no Total</small>
                        @php
                            $participationCredits = $totalCredits > 0 ? ($totalCredit / $totalCredits) * 100 : 0;
                            $participationDebits = $totalDebits > 0 ? ($totalDebit / $totalDebits) * 100 : 0;
                        @endphp
                        <div class="progress mb-1" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $participationCredits }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-success">{{ number_format($participationCredits, 1) }}% dos créditos</small>
                            <small class="text-danger">{{ number_format($participationDebits, 1) }}% dos débitos</small>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-1"></i>Categoria ativa
                        </small>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewCategoryDetails('{{ $category }}')">
                            <i class="fas fa-eye me-1"></i>Detalhes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Visualização em Tabela -->
<div id="tableView" style="display: none;">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-table text-primary me-2"></i>Dados Tabulares por Categoria
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="categoriesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-4 py-3">
                                <i class="fas fa-tag me-2 text-muted"></i>Categoria
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-arrow-up me-2 text-success"></i>Créditos
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-arrow-down me-2 text-danger"></i>Débitos
                            </th>
                            <th class="border-0 px-4 py-3 text-end">
                                <i class="fas fa-balance-scale me-2 text-info"></i>Saldo
                            </th>
                            <th class="border-0 px-4 py-3 text-center">
                                <i class="fas fa-list me-2 text-muted"></i>Qtd. Transações
                            </th>
                            <th class="border-0 px-4 py-3 text-center">
                                <i class="fas fa-percentage me-2 text-muted"></i>Participação
                            </th>
                            <th class="border-0 px-4 py-3 text-center">
                                <i class="fas fa-cog me-2 text-muted"></i>Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorySummary as $category => $types)
                        <tr class="category-row">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-25 rounded p-2 me-3">
                                        <i class="fas fa-tag text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $category ?: 'Sem categoria' }}</strong>
                                    </div>
                                </div>
                            </td>
                            @php
                                $creditData = $types->firstWhere('type', 'credit');
                                $debitData = $types->firstWhere('type', 'debit');
                                $totalCredit = $creditData->total ?? 0;
                                $totalDebit = $debitData->total ?? 0;
                                $balance = $totalCredit - $totalDebit;
                                $creditCount = $creditData->count ?? 0;
                                $debitCount = $debitData->count ?? 0;
                                $totalTransactions = $creditCount + $debitCount;
                            @endphp
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold text-success">
                                    R$ {{ number_format($totalCredit, 2, ',', '.') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $creditCount }} operações</small>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold text-danger">
                                    R$ {{ number_format($totalDebit, 2, ',', '.') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $debitCount }} operações</small>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-{{ $balance >= 0 ? 'plus' : 'minus' }}-circle me-1"></i>
                                    R$ {{ number_format(abs($balance), 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-primary fs-6">{{ $totalTransactions }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $totalValue = $totalCredit + $totalDebit;
                                    $allValues = $categorySummary->map(function($types) {
                                        $credit = floatval($types->firstWhere('type', 'credit')->total ?? 0);
                                        $debit = floatval($types->firstWhere('type', 'debit')->total ?? 0);
                                        return $credit + $debit;
                                    })->sum();
                                    $participation = $allValues > 0 ? ($totalValue / $allValues) * 100 : 0;
                                @endphp
                                <div class="progress mb-1" style="width: 60px; height: 6px;">
                                    <div class="progress-bar bg-info" style="width: {{ $participation }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($participation, 1) }}%</small>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewCategoryDetails('{{ $category }}')" 
                                            title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="exportCategory('{{ $category }}')" 
                                            title="Exportar">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Visualização em Gráfico -->
<div id="chartView" style="display: none;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Distribuição por Categorias
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoriesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-list text-secondary me-2"></i>Ranking de Categorias
                    </h6>
                </div>
                <div class="card-body">
                    <div id="categoryRanking">
                        @php
                            $sortedCategories = $categorySummary->map(function($types, $category) {
                                $credit = floatval($types->firstWhere('type', 'credit')->total ?? 0);
                                $debit = floatval($types->firstWhere('type', 'debit')->total ?? 0);
                                return [
                                    'name' => $category ?: 'Sem categoria',
                                    'total' => $credit + $debit,
                                    'balance' => $credit - $debit,
                                    'credit' => $credit,
                                    'debit' => $debit
                                ];
                            })->sortByDesc('total')->take(10);
                        @endphp
                        
                        @foreach($sortedCategories as $index => $cat)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 32px; height: 32px;">
                                <strong class="small">{{ $index + 1 }}</strong>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="small">{{ $cat['name'] }}</strong>
                                <br>
                                <small class="text-muted">R$ {{ number_format($cat['total'], 2, ',', '.') }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $cat['balance'] >= 0 ? 'success' : 'danger' }} bg-opacity-25 text-{{ $cat['balance'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $cat['balance'] >= 0 ? '+' : '-' }}R$ {{ number_format(abs($cat['balance']), 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<!-- Estado Vazio -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-chart-pie fa-4x text-muted mb-4"></i>
        <h4 class="text-muted">Nenhuma categoria encontrada</h4>
        <p class="text-muted mb-4">
            Não há transações categorizadas no período selecionado.<br>
            Ajuste os filtros ou verifique se existem transações cadastradas.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-outline-primary" onclick="adjustFilters()">
                <i class="fas fa-filter me-2"></i>Ajustar Filtros
            </button>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Ver Transações
            </a>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.category-card:hover .card {
    transform: translateY(-5px);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .category-card {
        margin-bottom: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

.btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.btn-group .btn:not(.active):hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Inicializar filtros e busca
    $('#searchCategories, #filterBalance, #sortBy, #sortOrder').on('change keyup', function() {
        filterAndSort();
    });
    
    // Atalhos de teclado
    $(document).on('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.which) {
                case 49: // Ctrl+1 - Cards
                    e.preventDefault();
                    toggleView('cards');
                    break;
                case 50: // Ctrl+2 - Tabela
                    e.preventDefault();
                    toggleView('table');
                    break;
                case 51: // Ctrl+3 - Gráfico
                    e.preventDefault();
                    toggleView('chart');
                    break;
                case 70: // Ctrl+F - Buscar
                    e.preventDefault();
                    $('#searchCategories').focus();
                    break;
            }
        }
    });
    
    // Inicializar gráfico se há dados
    @if($categorySummary->count() > 0)
        initializeChart();
    @endif
});

function setQuickPeriod(period) {
    const today = new Date();
    let startDate, endDate = today;
    
    switch(period) {
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            break;
        case 'last12':
            startDate = new Date(today.getFullYear() - 1, today.getMonth(), 1);
            break;
    }
    
    $('input[name="date_from"]').val(startDate.toISOString().split('T')[0]);
    $('input[name="date_to"]').val(endDate.toISOString().split('T')[0]);
}

function toggleView(view) {
    // Ocultar todas as views
    $('#cardsView, #tableView, #chartView').hide();
    $('.btn-group .btn').removeClass('active');
    
    // Mostrar view selecionada
    switch(view) {
        case 'cards':
            $('#cardsView').show();
            $('#cardsBtn').addClass('active');
            break;
        case 'table':
            $('#tableView').show();
            $('#tableBtn').addClass('active');
            break;
        case 'chart':
            $('#chartView').show();
            $('#chartBtn').addClass('active');
            break;
    }
}

function filterAndSort() {
    const searchTerm = $('#searchCategories').val().toLowerCase();
    const balanceFilter = $('#filterBalance').val();
    const sortBy = $('#sortBy').val();
    const sortOrder = $('#sortOrder').val();
    
    // Filtrar cards
    $('.category-card').each(function() {
        const card = $(this);
        const categoryName = card.data('category');
        const cardText = card.text().toLowerCase();
        const hasPositiveBalance = card.find('.text-success').length > 0;
        
        let show = true;
        
        if (searchTerm && !cardText.includes(searchTerm)) show = false;
        if (balanceFilter === 'positive' && !hasPositiveBalance) show = false;
        if (balanceFilter === 'negative' && hasPositiveBalance) show = false;
        
        card.toggle(show);
    });
    
    // Filtrar tabela
    $('.category-row').each(function() {
        const row = $(this);
        const rowText = row.text().toLowerCase();
        const hasPositiveBalance = row.find('.text-success').length > 0;
        
        let show = true;
        
        if (searchTerm && !rowText.includes(searchTerm)) show = false;
        if (balanceFilter === 'positive' && !hasPositiveBalance) show = false;
        if (balanceFilter === 'negative' && hasPositiveBalance) show = false;
        
        row.toggle(show);
    });
}

function clearFilters() {
    $('#searchCategories').val('');
    $('#filterBalance').val('');
    $('#sortBy').val('name');
    $('#sortOrder').val('desc');
    $('.category-card, .category-row').show();
}

function initializeChart() {
    const ctx = document.getElementById('categoriesChart').getContext('2d');
    const categories = [];
    const creditData = [];
    const debitData = [];
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
    ];
    
    @foreach($categorySummary as $category => $types)
        categories.push('{{ $category ?: "Sem categoria" }}');
        creditData.push({{ $types->firstWhere('type', 'credit')->total ?? 0 }});
        debitData.push({{ $types->firstWhere('type', 'debit')->total ?? 0 }});
    @endforeach
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{
                label: 'Créditos',
                data: creditData,
                backgroundColor: colors.slice(0, categories.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            return label + ': R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        });
}

function viewCategoryDetails(category) {
    // Implementar modal ou redirecionamento para detalhes da categoria
    alert(`Visualizando detalhes da categoria: ${category}`);
}

function exportCategory(category) {
    const params = new URLSearchParams(window.location.search);
    params.set('category', category);
    
    // Construir URL de exportação
    const baseUrl = '/bc/reports/export/categories/excel';
    const url = baseUrl + '?' + params.toString();
    
    window.open(url, '_blank');
}

function exportData(format) {
    const params = new URLSearchParams(window.location.search);
    
    // Construir URL de exportação
    const baseUrl = '/bc/reports/export/categories/' + format;
    const url = baseUrl + '?' + params.toString();
    
    window.open(url, '_blank');
}

function adjustFilters() {
    $('input[name="date_from"]').focus();
}
</script>
@endpush
