@extends('layouts.app')

@section('title', 'Fornecedores')

@section('header')
<div class="d-flex align-items-center">
    <i class="fas fa-truck text-primary me-3" style="font-size: 2rem;"></i>
    <div>
        <h2 class="mb-0">Gestão de Fornecedores</h2>
        <p class="text-muted mb-0">Gerencie seus fornecedores e parcerias comerciais</p>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Estatísticas -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Total de Fornecedores</h6>
                        <h2 class="mb-0">{{ $stats['total'] }}</h2>
                    </div>
                    <i class="fas fa-truck fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Fornecedores Ativos</h6>
                        <h2 class="mb-0">{{ $stats['active'] }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Fornecedores Inativos</h6>
                        <h2 class="mb-0">{{ $stats['inactive'] }}</h2>
                    </div>
                    <i class="fas fa-pause-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Novos este Mês</h6>
                        <h2 class="mb-0">{{ $suppliers->where('created_at', '>=', now()->startOfMonth())->count() }}</h2>
                    </div>
                    <i class="fas fa-plus-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Fornecedores
                </h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Novo Fornecedor
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nome, email, documento ou contato..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Documento</th>
                        <th>Contato</th>
                        <th>Cidade</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-2">
                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $supplier->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $supplier->email ?: '-' }}</td>
                        <td>{{ $supplier->phone ?: '-' }}</td>
                        <td>{{ $supplier->formatted_document ?: '-' }}</td>
                        <td>{{ $supplier->contact_person ?: '-' }}</td>
                        <td>{{ $supplier->city ?: '-' }}</td>
                        <td>
                            @if($supplier->active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $supplier->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('suppliers.show', $supplier) }}" 
                                   class="btn btn-outline-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" 
                                   class="btn btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete({{ $supplier->id }})" title="Remover">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-truck fa-3x mb-3"></i>
                                <p>Nenhum fornecedor encontrado</p>
                                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Fornecedor
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($suppliers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover este fornecedor?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(supplierId) {
    const form = document.getElementById('deleteForm');
    form.action = `/suppliers/${supplierId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
