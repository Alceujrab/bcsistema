@extends('layouts.app')

@section('title', 'Dashboard - Teste')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Dashboard - Sistema BC</h1>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total de Transações</h5>
                            <h2>{{ $stats['total_transactions'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Entradas</h5>
                            <h2>R$ {{ number_format($stats['total_income'] ?? 0, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Saídas</h5>
                            <h2>R$ {{ number_format($stats['total_expense'] ?? 0, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Saldo</h5>
                            <h2>R$ {{ number_format($stats['balance'] ?? 0, 2, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Transações Recentes</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Conta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                            </td>
                                            <td>{{ $transaction->bankAccount->name ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Nenhuma transação encontrada</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
