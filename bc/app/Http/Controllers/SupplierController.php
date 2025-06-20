<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }

        $suppliers = $query->orderBy('name')->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('active', true)->count(),
            'inactive' => Supplier::where('active', false)->count(),
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|in:cpf,cnpj',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('accountPayables');
        
        // Estatísticas do fornecedor
        $stats = [
            'total_payables' => $supplier->accountPayables->count(),
            'pending_amount' => $supplier->accountPayables->where('status', 'pending')->sum('remaining_amount'),
            'overdue_count' => $supplier->accountPayables->where('status', 'overdue')->count(),
        ];

        return view('suppliers.show', compact('supplier', 'stats'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|in:cpf,cnpj',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $supplier->update($request->all());

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')
                ->with('success', 'Fornecedor removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Erro ao remover fornecedor. Verifique se não há contas a pagar associadas.');
        }
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['active' => !$supplier->active]);
        
        $status = $supplier->active ? 'ativado' : 'desativado';
        return redirect()->back()
            ->with('success', "Fornecedor {$status} com sucesso!");
    }
}
