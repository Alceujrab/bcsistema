@extends('reports.pdf.layout')

@section('title', 'Relatório do Dashboard')

@section('content')
    <div class="report-info">
        <h3>Resumo Executivo do Sistema</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Data de Geração:</span>
                <span class="info-value">{{ $generated_at }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Período de Referência:</span>
                <span class="info-value">{{ now()->format('m/Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Estatísticas Principais -->
    <div class="summary-box">
        <h3>Estatísticas Gerais do Sistema</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total de Contas</span>
                <span class="summary-value">{{ $stats['total_accounts'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Contas Ativas</span>
                <span class="summary-value text-success">{{ $stats['active_accounts'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total de Transações</span>
                <span class="summary-value">{{ number_format($stats['total_transactions'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Saldo Total</span>
                <span class="summary-value {{ $stats['total_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    R$ {{ number_format($stats['total_balance'], 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Estatísticas do Mês Atual -->
    <div class="summary-box">
        <h3>Resumo do Mês Atual ({{ now()->format('m/Y') }})</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Transações do Mês</span>
                <span class="summary-value">{{ number_format($monthlyStats['current_month_transactions'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Créditos</span>
                <span class="summary-value text-success">R$ {{ number_format($monthlyStats['current_month_credit'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Débitos</span>
                <span class="summary-value text-danger">R$ {{ number_format($monthlyStats['current_month_debit'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Saldo Mensal</span>
                <span class="summary-value {{ ($monthlyStats['current_month_credit'] - $monthlyStats['current_month_debit']) >= 0 ? 'text-success' : 'text-danger' }}">
                    R$ {{ number_format($monthlyStats['current_month_credit'] - $monthlyStats['current_month_debit'], 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Indicadores de Performance -->
    <div class="summary-box">
        <h3>Indicadores de Performance</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Média Diária de Transações</span>
                <span class="summary-value">{{ $monthlyStats['daily_average'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Transações Pendentes</span>
                <span class="summary-value text-warning">{{ $stats['pending_transactions'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Conciliações do Mês</span>
                <span class="summary-value text-info">{{ $stats['month_reconciliations'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total de Categorias</span>
                <span class="summary-value">{{ $stats['total_categories'] }}</span>
            </div>
        </div>
    </div>

    <!-- Transações Recentes -->
    @if($recentTransactions->count() > 0)
        <h3>Transações Recentes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Conta</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions->take(10) as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($transaction->description, 35) }}</td>
                        <td>{{ $transaction->bankAccount->name ?? 'N/A' }}</td>
                        <td class="text-right {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type == 'credit' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type == 'credit' ? 'C' : 'D' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @switch($transaction->status)
                                @case('reconciled')
                                    <span class="text-success">Conciliado</span>
                                    @break
                                @case('pending')
                                    <span class="text-warning">Pendente</span>
                                    @break
                                @default
                                    <span class="text-info">{{ ucfirst($transaction->status) }}</span>
                            @endswitch
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Conciliações Recentes -->
    @if($recentReconciliations->count() > 0)
        <div class="page-break"></div>
        <h3>Conciliações Recentes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Conta</th>
                    <th>Data</th>
                    <th class="text-right">Saldo Inicial</th>
                    <th class="text-right">Saldo Final</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentReconciliations->take(8) as $reconciliation)
                    <tr>
                        <td>{{ $reconciliation->id }}</td>
                        <td>{{ $reconciliation->bankAccount->name ?? 'N/A' }}</td>
                        <td>{{ $reconciliation->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">R$ {{ number_format($reconciliation->opening_balance, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($reconciliation->closing_balance, 2, ',', '.') }}</td>
                        <td class="text-center">
                            @switch($reconciliation->status)
                                @case('approved')
                                    <span class="text-success">Aprovado</span>
                                    @break
                                @case('completed')
                                    <span class="text-info">Concluído</span>
                                    @break
                                @case('draft')
                                    <span class="text-warning">Rascunho</span>
                                    @break
                                @default
                                    <span>{{ ucfirst($reconciliation->status) }}</span>
                            @endswitch
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Top Categorias -->
    @if(isset($monthlyStats['top_categories']) && $monthlyStats['top_categories']->count() > 0)
        <div class="summary-box">
            <h3>Top Categorias</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="text-right">Quantidade</th>
                        <th class="text-right">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyStats['top_categories']->take(5) as $category)
                        <tr>
                            <td>{{ $category->category_id ?: 'Sem categoria' }}</td>
                            <td class="text-right">{{ $category->count }}</td>
                            <td class="text-right">R$ {{ number_format($category->total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Observações -->
    <div class="report-info">
        <h3>Observações</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Sistema:</span>
                <span class="info-value">BC Sistema Financeiro</span>
            </div>
            <div class="info-item">
                <span class="info-label">Usuário:</span>
                <span class="info-value">{{ auth()->user()->name ?? 'Sistema' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Última Atualização:</span>
                <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>
@endsection
