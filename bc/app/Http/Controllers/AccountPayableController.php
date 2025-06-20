<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Models\Supplier;
use App\Helpers\CategoryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountPayableController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountPayable::with('supplier');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Ordenação
        $sortBy = $request->get('sort', 'due_date');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $accountPayables = $query->paginate(15);

        // Dados para filtros
        $suppliers = Supplier::active()->orderBy('name')->get();

        // Estatísticas
        $stats = [
            'total' => AccountPayable::count(),
            'pending' => AccountPayable::where('status', 'pending')->count(),
            'overdue' => AccountPayable::where('status', 'overdue')->count(),
            'paid' => AccountPayable::where('status', 'paid')->count(),
            'pending_amount' => AccountPayable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountPayable::where('status', 'overdue')->sum('amount'),
        ];

        return view('account-payables.index', compact('accountPayables', 'suppliers', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = CategoryHelper::getPayableCategories();
        return view('account-payables.create', compact('suppliers', 'categories'));
    }

    public function store(Request $request)
    {
        $validCategories = array_keys(CategoryHelper::getPayableCategories());
        
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'document_number' => 'nullable|string|max:100',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        AccountPayable::create($request->all());

        return redirect()->route('account-payables.index')
            ->with('success', 'Conta a pagar cadastrada com sucesso!');
    }

    public function show(AccountPayable $accountPayable)
    {
        $accountPayable->load('supplier');
        return view('account-payables.show', compact('accountPayable'));
    }

    public function edit(AccountPayable $accountPayable)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = CategoryHelper::getPayableCategories();
        return view('account-payables.edit', compact('accountPayable', 'suppliers', 'categories'));
    }

    public function update(Request $request, AccountPayable $accountPayable)
    {
        $validCategories = array_keys(CategoryHelper::getPayableCategories());
        
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'document_number' => 'nullable|string|max:100',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $accountPayable->update($request->all());

        return redirect()->route('account-payables.show', $accountPayable)
            ->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(AccountPayable $accountPayable)
    {
        try {
            $accountPayable->delete();
            return redirect()->route('account-payables.index')
                ->with('success', 'Conta removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('account-payables.index')
                ->with('error', 'Erro ao remover conta.');
        }
    }

    public function markAsPaid(AccountPayable $accountPayable)
    {
        $accountPayable->update([
            'status' => 'paid',
            'payment_date' => now(),
            'paid_amount' => $accountPayable->amount
        ]);

        return redirect()->back()
            ->with('success', 'Conta marcada como paga!');
    }

    public function partialPayment(Request $request, AccountPayable $accountPayable)
    {
        $validator = Validator::make($request->all(), [
            'paid_amount' => 'required|numeric|min:0.01|max:' . $accountPayable->remaining_amount,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $newPaidAmount = $accountPayable->paid_amount + $request->paid_amount;
        $status = $newPaidAmount >= $accountPayable->amount ? 'paid' : 'partial';

        $accountPayable->update([
            'paid_amount' => $newPaidAmount,
            'status' => $status,
            'payment_date' => $status === 'paid' ? now() : $accountPayable->payment_date
        ]);

        return redirect()->back()
            ->with('success', 'Pagamento parcial registrado com sucesso!');
    }

    public function overdue()
    {
        $accountPayables = AccountPayable::with('supplier')
            ->overdue()
            ->orderBy('due_date')
            ->paginate(15);

        return view('account-payables.overdue', compact('accountPayables'));
    }
}
