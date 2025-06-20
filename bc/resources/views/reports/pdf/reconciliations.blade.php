@extends('reports.pdf.layout')

@section('title', 'Relatório de Conciliações')

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
            @if(isset($filters['status']) && $filters['status'])
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ ucfirst($filters['status']) }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total</span>
                <span class="summary-value">{{ $stats['total'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Aprovadas</span>
                <span class="summary-value text-success">{{ $stats['approved'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Pendentes</span>
                <span class="summary-value text-warning">{{ $stats['pending'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Taxa Aprovação</span>
                <span class="summary-value text-info">
                    {{ $stats['total'] > 0 ? number_format(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Conta</th>
                <th>Período</th>
                <th class="text-right">Saldo Inicial</th>
                <th class="text-right">Saldo Final</th>
                <th class="text-right">Diferença</th>
                <th class="text-center">Status</th>
                <th>Criado em</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reconciliations as $reconciliation)
                <tr>
                    <td>{{ $reconciliation->id }}</td>
                    <td>{{ $reconciliation->bankAccount->name ?? 'N/A' }}</td>
                    <td>
                        {{ $reconciliation->period_start->format('d/m/Y') }} - 
                        {{ $reconciliation->period_end->format('d/m/Y') }}
                    </td>
                    <td class="text-right">R$ {{ number_format($reconciliation->opening_balance, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($reconciliation->closing_balance, 2, ',', '.') }}</td>
                    <td class="text-right {{ $reconciliation->difference_amount == 0 ? 'text-success' : 'text-warning' }}">
                        R$ {{ number_format($reconciliation->difference_amount, 2, ',', '.') }}
                    </td>
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
                            @case('rejected')
                                <span class="text-danger">Rejeitado</span>
                                @break
                            @default
                                <span>{{ ucfirst($reconciliation->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $reconciliation->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Nenhuma conciliação encontrada com os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($reconciliations->count() > 30)
        <div class="page-break"></div>
    @endif
@endsection
