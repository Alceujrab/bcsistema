@extends('layouts.app')

@section('title', 'Comparação de Contas')

@section('header')
<div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="{{ route('account-management.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-0">Comparação de Contas</h2>
            <p class="text-muted mb-0">Análise comparativa entre {{ $comparison->count() }} contas</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    @foreach($comparison as $index => $data)
    <div class="col-lg-{{ 12 / $comparison->count() }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-{{ ['primary', 'success', 'warning', 'info', 'secondary'][$index % 5] }} text-white">
                <div class="d-flex align-items-center">
                    @switch($data['account']->type)
                        @case('checking')
                            <i class="fas fa-university me-2"></i>
                            @break
                        @case('savings')
                            <i class="fas fa-piggy-bank me-2"></i>
                            @break
                        @case('credit_card')
                            <i class="fas fa-credit-card me-2"></i>
                            @break
                        @case('investment')
                            <i class="fas fa-chart-line me-2"></i>
                            @break
                    @endswitch
                    <div>
                        <h6 class="mb-0">{{ $data['account']->name }}</h6>
                        <small class="opacity-75">{{ $data['account']->bank_name }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Saldo Atual -->
                <div class="text-center mb-3">
                    <h4 class="mb-1 {{ $data['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                        R$ {{ number_format($data['balance'], 2, ',', '.') }}
                    </h4>
                    <small class="text-muted">Saldo Atual</small>
                </div>
                
                <!-- Estatísticas -->
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h6 class="text-success mb-1">R$ {{ number_format($data['total_credit'], 2, ',', '.') }}</h6>
                            <small class="text-muted">Entradas</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h6 class="text-danger mb-1">R$ {{ number_format($data['total_debit'], 2, ',', '.') }}</h6>
                        <small class="text-muted">Saídas</small>
                    </div>
                </div>
                
                <hr>
                
                <!-- Métricas Adicionais -->
                <div class="row text-center">
                    <div class="col-6 mb-2">
                        <div class="border-end">
                            <strong class="d-block">{{ $data['transaction_count'] }}</strong>
                            <small class="text-muted">Transações</small>
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <strong class="d-block {{ $data['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($data['net_flow'], 2, ',', '.') }}
                        </strong>
                        <small class="text-muted">Fluxo Líquido</small>
                    </div>
                </div>
                
                <div class="text-center">
                    <strong class="d-block text-info">R$ {{ number_format($data['avg_transaction'], 2, ',', '.') }}</strong>
                    <small class="text-muted">Média por Transação</small>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex gap-2">
                    <a href="{{ route('account-management.show', $data['account']) }}" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-eye me-1"></i>Detalhes
                    </a>
                    <a href="{{ route('bank-accounts.edit', $data['account']) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Gráfico Comparativo -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Comparação Visual
                </h5>
            </div>
            <div class="card-body">
                <canvas id="comparisonChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Rankings
                </h5>
            </div>
            <div class="card-body">
                <!-- Maior Saldo -->
                <div class="mb-4">
                    <h6 class="text-primary">💰 Maior Saldo</h6>
                    @php $maxBalance = $comparison->sortByDesc('balance')->first(); @endphp
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>{{ $maxBalance['account']->name }}</strong>
                            <div class="text-success">R$ {{ number_format($maxBalance['balance'], 2, ',', '.') }}</div>
                        </div>
                        <i class="fas fa-crown text-warning"></i>
                    </div>
                </div>
                
                <!-- Mais Movimentada -->
                <div class="mb-4">
                    <h6 class="text-info">📈 Mais Movimentada</h6>
                    @php $maxTransactions = $comparison->sortByDesc('transaction_count')->first(); @endphp
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>{{ $maxTransactions['account']->name }}</strong>
                            <div class="text-info">{{ $maxTransactions['transaction_count'] }} transações</div>
                        </div>
                        <i class="fas fa-fire text-danger"></i>
                    </div>
                </div>
                
                <!-- Melhor Fluxo -->
                <div class="mb-4">
                    <h6 class="text-success">📊 Melhor Fluxo</h6>
                    @php $maxFlow = $comparison->sortByDesc('net_flow')->first(); @endphp
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>{{ $maxFlow['account']->name }}</strong>
                            <div class="text-success">R$ {{ number_format($maxFlow['net_flow'], 2, ',', '.') }}</div>
                        </div>
                        <i class="fas fa-chart-line text-success"></i>
                    </div>
                </div>
                
                <!-- Maior Média -->
                <div>
                    <h6 class="text-warning">💸 Maior Média</h6>
                    @php $maxAvg = $comparison->sortByDesc('avg_transaction')->first(); @endphp
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>{{ $maxAvg['account']->name }}</strong>
                            <div class="text-warning">R$ {{ number_format($maxAvg['avg_transaction'], 2, ',', '.') }}</div>
                        </div>
                        <i class="fas fa-medal text-gold"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela Comparativa Detalhada -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Comparação Detalhada
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Conta</th>
                                <th>Tipo</th>
                                <th class="text-end">Saldo Atual</th>
                                <th class="text-end">Total Entradas</th>
                                <th class="text-end">Total Saídas</th>
                                <th class="text-end">Fluxo Líquido</th>
                                <th class="text-center">Transações</th>
                                <th class="text-end">Média/Transação</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparison as $data)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @switch($data['account']->type)
                                            @case('checking')
                                                <i class="fas fa-university text-primary me-2"></i>
                                                @break
                                            @case('savings')
                                                <i class="fas fa-piggy-bank text-success me-2"></i>
                                                @break
                                            @case('credit_card')
                                                <i class="fas fa-credit-card text-warning me-2"></i>
                                                @break
                                            @case('investment')
                                                <i class="fas fa-chart-line text-info me-2"></i>
                                                @break
                                        @endswitch
                                        <div>
                                            <strong>{{ $data['account']->name }}</strong>
                                            <div class="small text-muted">{{ $data['account']->bank_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $data['account']->type_name }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $data['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        R$ {{ number_format($data['balance'], 2, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="text-end text-success">
                                    R$ {{ number_format($data['total_credit'], 2, ',', '.') }}
                                </td>
                                <td class="text-end text-danger">
                                    R$ {{ number_format($data['total_debit'], 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $data['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        R$ {{ number_format($data['net_flow'], 2, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $data['transaction_count'] }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-info">R$ {{ number_format($data['avg_transaction'], 2, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($data['account']->active)
                                        <span class="badge bg-success">Ativa</span>
                                    @else
                                        <span class="badge bg-secondary">Inativa</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const comparisonData = @json($comparison->values());
    
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: comparisonData.map(item => item.account.name),
            datasets: [{
                label: 'Saldo Atual',
                data: comparisonData.map(item => item.balance),
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            }, {
                label: 'Total Entradas',
                data: comparisonData.map(item => item.total_credit),
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Total Saídas',
                data: comparisonData.map(item => item.total_debit),
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
