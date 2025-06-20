@extends('reports.pdf.layout')

@section('title', 'Relatório de Transações')

@section('content')
    <div class="report-info">
        <h3>Informações do Relatório</h3>
        <div class="info-grid">
            @if(isset($filters['date_from']) && $filters['date_from'])
                <div class="info-item">
                    <span class="info-label">Data Inicial:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}</span>
                </div>
            @endif
            @if(isset($filters['date_to']) && $filters['date_to'])
                <div class="info-item">
                    <span class="info-label">Data Final:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</span>
                </div>
            @endif
            @if(isset($filters['bank_account_id']) && $filters['bank_account_id'])
                <div class="info-item">
                    <span class="info-label">Conta Bancária:</span>
                    <span class="info-value">{{ $filters['bank_account_name'] ?? 'ID: ' . $filters['bank_account_id'] }}</span>
                </div>
            @endif
            @if(isset($filters['type']) && $filters['type'])
                <div class="info-item">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">{{ $filters['type'] == 'credit' ? 'Crédito' : 'Débito' }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total de Transações</span>
                <span class="summary-value">{{ number_format($summary['count'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Créditos</span>
                <span class="summary-value text-success">R$ {{ number_format($summary['total_credit'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Débitos</span>
                <span class="summary-value text-danger">R$ {{ number_format($summary['total_debit'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Saldo</span>
                <span class="summary-value {{ $summary['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    R$ {{ number_format($summary['balance'], 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Conta</th>
                <th class="text-right">Valor</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($transaction->description, 40) }}</td>
                    <td>{{ $transaction->category_id ?? 'Sem categoria' }}</td>
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
                            @case('cancelled')
                                <span class="text-danger">Cancelado</span>
                                @break
                            @default
                                <span class="text-info">{{ ucfirst($transaction->status) }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhuma transação encontrada com os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->count() > 50)
        <div class="page-break"></div>
    @endif
@endsection
