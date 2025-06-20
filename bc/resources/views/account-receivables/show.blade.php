@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-money-check-alt"></i> Detalhes da Conta a Receber</h4>
                    <div>
                        <a href="{{ route('account-receivables.edit', $accountReceivable->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('account-receivables.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Informações Básicas -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> Informações da Conta</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Cliente:</strong> {{ $accountReceivable->client->name }}</p>
                                            <p><strong>Descrição:</strong> {{ $accountReceivable->description }}</p>
                                            <p><strong>Valor Original:</strong> R$ {{ number_format($accountReceivable->amount, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Data de Vencimento:</strong> {{ $accountReceivable->due_date?->format('d/m/Y') ?? 'Não informado' }}</p>
                                            <p><strong>Data de Emissão:</strong> {{ $accountReceivable->issue_date?->format('d/m/Y') ?? 'Não informado' }}</p>
                                            <p><strong>Status:</strong> 
                                                @php
                                                    $statusClass = match($accountReceivable->status) {
                                                        'received' => 'success',
                                                        'pending' => 'warning',
                                                        'overdue' => 'danger',
                                                        'cancelled' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                    $statusText = match($accountReceivable->status) {
                                                        'received' => 'Recebido',
                                                        'pending' => 'Pendente',
                                                        'overdue' => 'Vencido',
                                                        'cancelled' => 'Cancelado',
                                                        default => 'Indefinido'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Valores e Cálculos -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-calculator"></i> Valores e Cálculos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Valor Original:</strong> R$ {{ number_format($accountReceivable->amount, 2, ',', '.') }}</p>
                                            <p><strong>Desconto:</strong> R$ {{ number_format($accountReceivable->discount ?? 0, 2, ',', '.') }}</p>
                                            <p><strong>Juros:</strong> R$ {{ number_format($accountReceivable->interest ?? 0, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $finalAmount = $accountReceivable->amount - ($accountReceivable->discount ?? 0) + ($accountReceivable->interest ?? 0);
                                            @endphp
                                            <p><strong>Valor Final:</strong> <span class="h5 text-success">R$ {{ number_format($finalAmount, 2, ',', '.') }}</span></p>
                                            <p><strong>Data de Recebimento:</strong> {{ $accountReceivable->payment_date?->format('d/m/Y') ?? 'Não recebido' }}</p>
                                            @if($accountReceivable->due_date && $accountReceivable->due_date->isPast() && $accountReceivable->status !== 'received')
                                                <p><strong>Dias em Atraso:</strong> 
                                                    <span class="text-danger">{{ now()->diffInDays($accountReceivable->due_date) }} dias</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalhes do Documento -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-alt"></i> Detalhes do Documento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Número da Fatura/NF:</strong> {{ $accountReceivable->invoice_number ?? 'Não informado' }}</p>
                                            <p><strong>Categoria:</strong> 
                                                @if($accountReceivable->category)
                                                    {{ ucfirst($accountReceivable->category) }}
                                                @else
                                                    Não informado
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($accountReceivable->due_date && $accountReceivable->due_date->isFuture() && $accountReceivable->status !== 'received')
                                                <p><strong>Vence em:</strong> {{ $accountReceivable->due_date->diffForHumans() }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            @if($accountReceivable->notes)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $accountReceivable->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Cliente -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-user"></i> Cliente</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nome:</strong> {{ $accountReceivable->client->name }}</p>
                                    <p><strong>Email:</strong> {{ $accountReceivable->client->email ?? 'Não informado' }}</p>
                                    <p><strong>Telefone:</strong> {{ $accountReceivable->client->phone ?? 'Não informado' }}</p>
                                    <a href="{{ route('clients.show', $accountReceivable->client->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver Cliente
                                    </a>
                                </div>
                            </div>

                            <!-- Resumo -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Resumo</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Criado em:</strong> {{ $accountReceivable->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Última atualização:</strong> {{ $accountReceivable->updated_at->format('d/m/Y H:i') }}</p>
                                    
                                    @if($accountReceivable->due_date)
                                        @if($accountReceivable->due_date->isFuture())
                                            <p><strong>Vence em:</strong> {{ $accountReceivable->due_date->diffForHumans() }}</p>
                                        @elseif($accountReceivable->status !== 'received')
                                            <p><strong>Vencido há:</strong> <span class="text-danger">{{ $accountReceivable->due_date->diffForHumans() }}</span></p>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Ações Rápidas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-lightning-bolt"></i> Ações Rápidas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($accountReceivable->status !== 'received')
                                            <form action="{{ route('account-receivables.update', $accountReceivable->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="received">
                                                <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-check"></i> Marcar como Recebido
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button class="btn btn-info btn-sm" onclick="window.print()">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>
                                        
                                        <button class="btn btn-primary btn-sm" onclick="generateInvoice()">
                                            <i class="fas fa-file-invoice"></i> Gerar Fatura
                                        </button>
                                        
                                        <form action="{{ route('account-receivables.destroy', $accountReceivable->id) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateInvoice() {
    alert('Funcionalidade de geração de fatura será implementada em breve!');
}
</script>
@endsection
