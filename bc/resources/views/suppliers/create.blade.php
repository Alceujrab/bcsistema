@extends('layouts.app')

@section('title', 'Novo Fornecedor')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-truck text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Novo Fornecedor</h2>
        <p class="text-muted mb-0">Cadastre um novo fornecedor no sistema</p>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Dados do Fornecedor
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Nome -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Nome da Empresa <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="(00) 00000-0000">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pessoa de Contato -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pessoa de Contato</label>
                            <input type="text" name="contact_person" 
                                   class="form-control @error('contact_person') is-invalid @enderror" 
                                   value="{{ old('contact_person') }}" placeholder="Nome do responsável">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipo de Documento -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo de Documento</label>
                            <select name="document_type" 
                                    class="form-select @error('document_type') is-invalid @enderror">
                                <option value="">Selecione</option>
                                <option value="cpf" {{ old('document_type') === 'cpf' ? 'selected' : '' }}>CPF</option>
                                <option value="cnpj" {{ old('document_type') === 'cnpj' ? 'selected' : '' }}>CNPJ</option>
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Documento -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Documento</label>
                            <input type="text" name="document" 
                                   class="form-control @error('document') is-invalid @enderror" 
                                   value="{{ old('document') }}" placeholder="000.000.000-00">
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Endereço -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Endereço</label>
                            <input type="text" name="address" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   value="{{ old('address') }}" placeholder="Rua, Número, Bairro">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cidade -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="city" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="state" class="form-select @error('state') is-invalid @enderror">
                                <option value="">Selecione</option>
                                <option value="AC" {{ old('state') === 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="AL" {{ old('state') === 'AL' ? 'selected' : '' }}>AL</option>
                                <option value="AP" {{ old('state') === 'AP' ? 'selected' : '' }}>AP</option>
                                <option value="AM" {{ old('state') === 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="BA" {{ old('state') === 'BA' ? 'selected' : '' }}>BA</option>
                                <option value="CE" {{ old('state') === 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="DF" {{ old('state') === 'DF' ? 'selected' : '' }}>DF</option>
                                <option value="ES" {{ old('state') === 'ES' ? 'selected' : '' }}>ES</option>
                                <option value="GO" {{ old('state') === 'GO' ? 'selected' : '' }}>GO</option>
                                <option value="MA" {{ old('state') === 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="MT" {{ old('state') === 'MT' ? 'selected' : '' }}>MT</option>
                                <option value="MS" {{ old('state') === 'MS' ? 'selected' : '' }}>MS</option>
                                <option value="MG" {{ old('state') === 'MG' ? 'selected' : '' }}>MG</option>
                                <option value="PA" {{ old('state') === 'PA' ? 'selected' : '' }}>PA</option>
                                <option value="PB" {{ old('state') === 'PB' ? 'selected' : '' }}>PB</option>
                                <option value="PR" {{ old('state') === 'PR' ? 'selected' : '' }}>PR</option>
                                <option value="PE" {{ old('state') === 'PE' ? 'selected' : '' }}>PE</option>
                                <option value="PI" {{ old('state') === 'PI' ? 'selected' : '' }}>PI</option>
                                <option value="RJ" {{ old('state') === 'RJ' ? 'selected' : '' }}>RJ</option>
                                <option value="RN" {{ old('state') === 'RN' ? 'selected' : '' }}>RN</option>
                                <option value="RS" {{ old('state') === 'RS' ? 'selected' : '' }}>RS</option>
                                <option value="RO" {{ old('state') === 'RO' ? 'selected' : '' }}>RO</option>
                                <option value="RR" {{ old('state') === 'RR' ? 'selected' : '' }}>RR</option>
                                <option value="SC" {{ old('state') === 'SC' ? 'selected' : '' }}>SC</option>
                                <option value="SP" {{ old('state') === 'SP' ? 'selected' : '' }}>SP</option>
                                <option value="SE" {{ old('state') === 'SE' ? 'selected' : '' }}>SE</option>
                                <option value="TO" {{ old('state') === 'TO' ? 'selected' : '' }}>TO</option>
                            </select>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CEP -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="zip_code" 
                                   class="form-control @error('zip_code') is-invalid @enderror" 
                                   value="{{ old('zip_code') }}" placeholder="00000-000">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Observações -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Informações adicionais sobre o fornecedor">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Fornecedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Máscara para telefone
document.querySelector('input[name="phone"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        value = value.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    }
    e.target.value = value;
});

// Máscara para CEP
document.querySelector('input[name="zip_code"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{5})(\d{3})$/, '$1-$2');
    e.target.value = value;
});

// Máscara para documento baseada no tipo
document.querySelector('select[name="document_type"]')?.addEventListener('change', function(e) {
    const documentInput = document.querySelector('input[name="document"]');
    const documentType = e.target.value;
    
    if (documentType === 'cpf') {
        documentInput.placeholder = '000.000.000-00';
        documentInput.maxLength = 14;
    } else if (documentType === 'cnpj') {
        documentInput.placeholder = '00.000.000/0000-00';
        documentInput.maxLength = 18;
    } else {
        documentInput.placeholder = '';
        documentInput.maxLength = 20;
    }
});

// Aplicar máscara ao documento
document.querySelector('input[name="document"]')?.addEventListener('input', function(e) {
    const documentType = document.querySelector('select[name="document_type"]').value;
    let value = e.target.value.replace(/\D/g, '');
    
    if (documentType === 'cpf' && value.length <= 11) {
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
        value = value.replace(/^(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3');
        value = value.replace(/^(\d{3})(\d{2})$/, '$1.$2');
    } else if (documentType === 'cnpj' && value.length <= 14) {
        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})$/, '$1.$2.$3/$4');
        value = value.replace(/^(\d{2})(\d{3})(\d{3})$/, '$1.$2.$3');
        value = value.replace(/^(\d{2})(\d{3})$/, '$1.$2');
    }
    
    e.target.value = value;
});
</script>
@endpush
