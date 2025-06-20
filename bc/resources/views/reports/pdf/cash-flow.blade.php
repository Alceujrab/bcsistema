@extends('reports.pdf.layout')

@section('title', 'Relatório de Fluxo de Caixa')

@section('content')
    <div class="report-info">
        <h3>Informações do Relatório</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Período:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
            </div>
            @if(isset($filters['bank_account_id']) && $filters['bank_account_id'])
                <div class="info-item">
                    <span class="info-label">Conta Bancária:</span>
                    <span class="info-value">{{ $filters['bank_account_name'] ?? 'ID: ' . $filters['bank_account_id'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Créditos</span>
                <span class="summary-value text-success">R$ {{ number_format($periodStats['total_credits'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Débitos</span>
                <span class="summary-value text-danger">R$ {{ number_format($periodStats['total_debits'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Fluxo Líquido</span>
                <span class="summary-value {{ $periodStats['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                    R$ {{ number_format($periodStats['net_flow'], 2, ',', '.') }}
                </span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Média Diária</span>
                <span class="summary-value text-info">R$ {{ number_format($periodStats['avg_daily_credits'] - $periodStats['avg_daily_debits'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th class="text-right">Créditos</th>
                <th class="text-right">Débitos</th>
                <th class="text-right">Saldo Diário</th>
                <th class="text-right">Saldo Cumulativo</th>
                <th class="text-center">Transações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyFlow as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->transaction_date)->format('d/m/Y') }}</td>
                    <td class="text-right text-success">
                        @if($day->credits > 0)
                            R$ {{ number_format($day->credits, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right text-danger">
                        @if($day->debits > 0)
                            R$ {{ number_format($day->debits, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right {{ $day->daily_balance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $day->daily_balance >= 0 ? '+' : '' }}R$ {{ number_format($day->daily_balance, 2, ',', '.') }}
                    </td>
                    <td class="text-right {{ $day->cumulative_balance >= 0 ? 'text-success' : 'text-danger' }}">
                        R$ {{ number_format($day->cumulative_balance, 2, ',', '.') }}
                    </td>
                    <td class="text-center">{{ $day->transaction_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhuma movimentação encontrada no período selecionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($dailyFlow->count() > 25)
        <div class="page-break"></div>
    @endif

    @if($dailyFlow->count() > 0)
        <div class="summary-box">
            <h3>Análise do Período</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Dias com Saldo Positivo:</span>
                    <span class="info-value text-success">{{ $dailyFlow->where('daily_balance', '>', 0)->count() }} dias</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dias com Saldo Negativo:</span>
                    <span class="info-value text-danger">{{ $dailyFlow->where('daily_balance', '<', 0)->count() }} dias</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Melhor Dia:</span>
                    <span class="info-value">
                        @if($dailyFlow->sortByDesc('daily_balance')->first())
                            {{ \Carbon\Carbon::parse($dailyFlow->sortByDesc('daily_balance')->first()->transaction_date)->format('d/m/Y') }}
                            (R$ {{ number_format($dailyFlow->sortByDesc('daily_balance')->first()->daily_balance, 2, ',', '.') }})
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pior Dia:</span>
                    <span class="info-value">
                        @if($dailyFlow->sortBy('daily_balance')->first())
                            {{ \Carbon\Carbon::parse($dailyFlow->sortBy('daily_balance')->first()->transaction_date)->format('d/m/Y') }}
                            (R$ {{ number_format($dailyFlow->sortBy('daily_balance')->first()->daily_balance, 2, ',', '.') }})
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endif
@endsection
