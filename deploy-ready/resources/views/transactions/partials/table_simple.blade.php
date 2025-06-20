<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Conta</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>
                    {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : 'N/A' }}
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="{{ $transaction->description }}">
                        {{ $transaction->description }}
                    </div>
                </td>
                <td>
                    {{ $transaction->bankAccount ? $transaction->bankAccount->name : 'N/A' }}
                </td>
                <td>
                    @if($transaction->type === 'credit')
                        <span class="badge bg-success">Crédito</span>
                    @else
                        <span class="badge bg-danger">Débito</span>
                    @endif
                </td>
                <td>
                    <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->type === 'credit' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                    </span>
                </td>
                <td>
                    @php
                        $statusClass = match($transaction->status ?? 'pending') {
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'warning'
                        };
                        $statusText = match($transaction->status ?? 'pending') {
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado',
                            default => 'Pendente'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <br>
                        Nenhuma transação encontrada
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
