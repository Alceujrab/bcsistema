<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('bankAccount');

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        $bankAccounts = BankAccount::where('active', true)->get();

        return view('transactions.index', compact('transactions', 'bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        return view('transactions.create', compact('bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'reference_number' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['import_hash'] = Transaction::generateImportHash($validated);
        
        $transaction = Transaction::create($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('bankAccount', 'reconciliation');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        return view('transactions.edit', compact('transaction', 'bankAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:pending,reconciled,error',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status === 'reconciled') {
            return back()->with('error', 'Não é possível excluir transações conciliadas.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transação excluída com sucesso!');
    }

    /**
     * Reconcile multiple transactions
     */
    public function reconcile(Request $request)
    {
        $transactionIds = $request->input('transaction_ids', []);
        
        Transaction::whereIn('id', $transactionIds)
            ->where('status', 'pending')
            ->update(['status' => 'reconciled']);

        return back()->with('success', count($transactionIds) . ' transações conciliadas!');
    }

    /**
     * Bulk categorize transactions
     */
    public function bulkCategorize(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array',
            'category' => 'required|string|max:100',
        ]);

        Transaction::whereIn('id', $validated['transaction_ids'])
            ->update(['category' => $validated['category']]);

        return back()->with('success', 'Categoria atualizada para as transações selecionadas!');
    }
}