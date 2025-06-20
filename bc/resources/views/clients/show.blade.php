@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-user"></i> Detalhes do Cliente</h4>
                    <div>
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
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
                                    <h5><i class="fas fa-info-circle"></i> Informações Básicas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nome:</strong> {{ $client->name }}</p>
                                            <p><strong>E-mail:</strong> {{ $client->email ?? 'Não informado' }}</p>
                                            <p><strong>Telefone:</strong> {{ $client->phone ?? 'Não informado' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>CPF/CNPJ:</strong> {{ $client->document ?? 'Não informado' }}</p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge bg-{{ $client->status == 'active' ? 'success' : 'danger' }}">
                                                    {{ $client->status == 'active' ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </p>
                                            <p><strong>Limite de Crédito:</strong> R$ {{ number_format($client->credit_limit ?? 0, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-map-marker-alt"></i> Endereço</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Endereço:</strong> {{ $client->address ?? 'Não informado' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Cidade:</strong> {{ $client->city ?? 'Não informada' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Estado:</strong> {{ $client->state ?? 'Não informado' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>CEP:</strong> {{ $client->zip_code ?? 'Não informado' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            @if($client->notes)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $client->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Estatísticas -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Estatísticas</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Cliente desde:</strong> {{ $client->created_at->format('d/m/Y') }}</p>
                                    <p><strong>Última atualização:</strong> {{ $client->updated_at->format('d/m/Y H:i') }}</p>
                                    
                                    <!-- Aqui podemos adicionar estatísticas de contas a receber quando implementadas -->
                                    <hr>
                                    <p><strong>Contas a Receber:</strong></p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-clock text-warning"></i> Pendentes: 0</li>
                                        <li><i class="fas fa-check text-success"></i> Pagas: 0</li>
                                        <li><i class="fas fa-exclamation-triangle text-danger"></i> Vencidas: 0</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Ações Rápidas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-lightning-bolt"></i> Ações Rápidas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('account-receivables.create', ['client_id' => $client->id]) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Nova Conta a Receber
                                        </a>
                                        <button class="btn btn-info btn-sm" onclick="window.print()">
                                            <i class="fas fa-print"></i> Imprimir Ficha
                                        </button>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-trash"></i> Excluir Cliente
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
