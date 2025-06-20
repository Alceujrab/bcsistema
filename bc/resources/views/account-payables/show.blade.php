@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-credit-card"></i> Detalhes da Conta a Pagar</h4>
                    <div>
                        <a href="{{ route('account-payables.edit', $accountPayable->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('account-payables.index') }}" class="btn btn-secondary">
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
                                            <p><strong>Fornecedor:</strong> {{ $accountPayable->supplier->name }}</p>
                                            <p><strong>Descrição:</strong> {{ $accountPayable->description }}</p>
                                            <p><strong>Valor:</strong> R$ {{ number_format($accountPayable->amount, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Data de Vencimento:</strong> {{ $accountPayable->due_date?->format('d/m/Y') ?? 'Não informado' }}</p>
                                            <p><strong>Data de Emissão:</strong> {{ $accountPayable->issue_date?->format('d/m/Y') ?? 'Não informado' }}</p>
                                            <p><strong>Status:</strong> 
                                                @php
                                                    $statusClass = match($accountPayable->status) {
                                                        'paid' => 'success',
                                                        'pending' => 'warning',
                                                        'overdue' => 'danger',
                                                        'cancelled' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                    $statusText = match($accountPayable->status) {
                                                        'paid' => 'Pago',
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

                            <!-- Detalhes do Documento -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-file-alt"></i> Detalhes do Documento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Número do Documento:</strong> {{ $accountPayable->document_number ?? 'Não informado' }}</p>
                                            <p><strong>Categoria:</strong> 
                                                @if($accountPayable->category)
                                                    {{ ucfirst($accountPayable->category) }}
                                                @else
                                                    Não informado
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Data de Pagamento:</strong> {{ $accountPayable->payment_date?->format('d/m/Y') ?? 'Não pago' }}</p>
                                            @if($accountPayable->due_date && $accountPayable->due_date->isPast() && $accountPayable->status !== 'paid')
                                                <p><strong>Dias em Atraso:</strong> 
                                                    <span class="text-danger">{{ now()->diffInDays($accountPayable->due_date) }} dias</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            @if($accountPayable->notes)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $accountPayable->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Fornecedor -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-truck"></i> Fornecedor</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nome:</strong> {{ $accountPayable->supplier->name }}</p>
                                    <p><strong>Email:</strong> {{ $accountPayable->supplier->email ?? 'Não informado' }}</p>
                                    <p><strong>Telefone:</strong> {{ $accountPayable->supplier->phone ?? 'Não informado' }}</p>
                                    <a href="{{ route('suppliers.show', $accountPayable->supplier->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver Fornecedor
                                    </a>
                                </div>
                            </div>

                            <!-- Resumo -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Resumo</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Criado em:</strong> {{ $accountPayable->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Última atualização:</strong> {{ $accountPayable->updated_at->format('d/m/Y H:i') }}</p>
                                    
                                    @if($accountPayable->due_date)
                                        @if($accountPayable->due_date->isFuture())
                                            <p><strong>Vence em:</strong> {{ $accountPayable->due_date->diffForHumans() }}</p>
                                        @elseif($accountPayable->status !== 'paid')
                                            <p><strong>Vencido há:</strong> <span class="text-danger">{{ $accountPayable->due_date->diffForHumans() }}</span></p>
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
                                        @if($accountPayable->status !== 'paid')
                                            <form action="{{ route('account-payables.update', $accountPayable->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="paid">
                                                <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-check"></i> Marcar como Pago
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button class="btn btn-info btn-sm" onclick="window.print()">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>
                                        
                                        <form action="{{ route('account-payables.destroy', $accountPayable->id) }}" method="POST" class="d-inline" 
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
@endsection
