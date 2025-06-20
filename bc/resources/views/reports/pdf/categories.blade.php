@extends('reports.pdf.layout')

@section('title', 'Relatório de Categorias')

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
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Categorias</span>
                <span class="summary-value">{{ $stats['total_categories'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Valor Total</span>
                <span class="summary-value text-info">R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Média por Categoria</span>
                <span class="summary-value text-info">R$ {{ number_format($stats['avg_per_category'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Categoria</th>
                <th class="text-center">Tipo</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Total</th>
                <th class="text-right">Média</th>
                <th class="text-right">Mínimo</th>
                <th class="text-right">Máximo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorySummary as $categoryId => $types)
                @foreach($types as $type)
                    <tr>
                        <td>{{ $categoryId ?: 'Sem categoria' }}</td>
                        <td class="text-center">
                            <span class="{{ $type->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $type->type == 'credit' ? 'Crédito' : 'Débito' }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($type->count, 0, ',', '.') }}</td>
                        <td class="text-right {{ $type->type == 'credit' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($type->total, 2, ',', '.') }}
                        </td>
                        <td class="text-right">R$ {{ number_format($type->average, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($type->min_amount, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($type->max_amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhuma categoria encontrada com os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($categorySummary->count() > 20)
        <div class="page-break"></div>
    @endif

    @if($categorySummary->count() > 0)
        <div class="summary-box">
            <h3>Top 5 Categorias por Volume</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Categoria</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">% do Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topCategories = $categorySummary->map(function($types, $categoryId) {
                            return [
                                'category' => $categoryId,
                                'total' => $types->sum('total')
                            ];
                        })->sortByDesc('total')->take(5);
                        $grandTotal = $stats['total_amount'];
                    @endphp
                    @foreach($topCategories as $index => $category)
                        <tr>
                            <td>{{ $index + 1 }}º</td>
                            <td>{{ $category['category'] ?: 'Sem categoria' }}</td>
                            <td class="text-right">R$ {{ number_format($category['total'], 2, ',', '.') }}</td>
                            <td class="text-right">
                                {{ $grandTotal > 0 ? number_format(($category['total'] / $grandTotal) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
