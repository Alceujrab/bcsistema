@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dashboard - Sistema Bancário</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Total de Contas -->
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total de Contas</h6>
                                            <h3 class="mb-0">{{ $totalAccounts ?? 0 }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-university fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total de Transações -->
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total de Transações</h6>
                                            <h3 class="mb-0">{{ $totalTransactions ?? 0 }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exchange-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Total -->
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Saldo Total</h6>
                                            <h3 class="mb-0">R$ {{ number_format($totalBalance ?? 0, 2, ',', '.') }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-wallet fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pendentes -->
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pendentes</h6>
                                            <h3 class="mb-0">{{ $pendingTransactions ?? 0 }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Links Rápidos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Acesso Rápido</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-list me-2"></i>
                                                Ver Transações
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('transactions.create') }}" class="btn btn-outline-success w-100">
                                                <i class="fas fa-plus me-2"></i>
                                                Nova Transação
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('bank-accounts.index') }}" class="btn btn-outline-info w-100">
                                                <i class="fas fa-university me-2"></i>
                                                Contas Bancárias
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100">
                                                <i class="fas fa-chart-bar me-2"></i>
                                                Relatórios
                                            </a>
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
</div>
@endsection
