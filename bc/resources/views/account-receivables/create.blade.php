@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-money-check-alt"></i> Nova Conta a Receber</h4>
                    <a href="{{ route('account-receivables.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('account-receivables.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="client_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-control @error('client_id') is-invalid @enderror" 
                                            id="client_id" name="client_id" required>
                                        <option value="">Selecione um cliente</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                           id="description" name="description" value="{{ old('description') }}" required>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="due_date" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="issue_date" class="form-label">Data de Emissão</label>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                           id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}">
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="invoice_number" class="form-label">Número da Fatura/NF</label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                           id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}">
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category" class="form-label">Categoria</label>
                                    <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>Recebido</option>
                                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Vencido</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_date" class="form-label">Data de Recebimento</label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                           id="payment_date" name="payment_date" value="{{ old('payment_date') }}">
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="discount" class="form-label">Desconto</label>
                                    <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror" 
                                           id="discount" name="discount" value="{{ old('discount', 0) }}">
                                    @error('discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="interest" class="form-label">Juros</label>
                                    <input type="number" step="0.01" class="form-control @error('interest') is-invalid @enderror" 
                                           id="interest" name="interest" value="{{ old('interest', 0) }}">
                                    @error('interest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Conta a Receber
                            </button>
                            <a href="{{ route('account-receivables.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const paymentDateField = document.getElementById('payment_date');
    
    statusSelect.addEventListener('change', function() {
        if (this.value === 'received' && !paymentDateField.value) {
            paymentDateField.value = new Date().toISOString().split('T')[0];
        }
    });
});
</script>
@endsection
