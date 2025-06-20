<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = BankAccount::with('transactions')
            ->withCount('transactions')
            ->get();
            
        return view('bank-accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'agency' => 'nullable|string|max:20',
            'type' => 'required|in:checking,savings,credit_card',
            'balance' => 'nullable|numeric',
        ]);

        $account = BankAccount::create($validated);

        return redirect()->route('bank-accounts.show', $account)
            ->with('success', 'Conta bancária criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['transactions' => function ($query) {
            $query->latest('transaction_date')->limit(50);
        }]);

        $stats = [
            'total_credit' => $bankAccount->transactions()->where('type', 'credit')->sum('amount'),
            'total_debit' => $bankAccount->transactions()->where('type', 'debit')->sum('amount'),
            'pending_count' => $bankAccount->transactions()->pending()->count(),
            'reconciled_count' => $bankAccount->transactions()->reconciled()->count(),
        ];

        return view('bank-accounts.show', compact('bankAccount', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'agency' => 'nullable|string|max:20',
            'type' => 'required|in:checking,savings,credit_card',
            'active' => 'boolean',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.show', $bankAccount)
            ->with('success', 'Conta bancária atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->transactions()->count() > 0) {
            return back()->with('error', 'Não é possível excluir conta com transações.');
        }

        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária excluída com sucesso!');
    }
}