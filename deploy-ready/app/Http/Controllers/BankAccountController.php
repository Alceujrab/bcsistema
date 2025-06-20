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
        $accounts = BankAccount::withCount('transactions')
            ->withSum('transactions as total_credit', 'amount')
            ->withSum('transactions as total_debit', 'amount')
            ->orderBy('name')
            ->get(); // Mudado para get() para evitar erro de paginação
            
        // Estatísticas gerais
        $stats = [
            'total_accounts' => $accounts->count(),
            'active_accounts' => $accounts->where('active', true)->count(),
            'total_balance' => $accounts->sum('balance'),
            'total_transactions' => $accounts->sum('transactions_count'),
        ];
        
        return view('bank-accounts.index', compact('accounts', 'stats'));
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
            'type' => 'required|in:checking,savings,investment,credit_card',
            'balance' => 'nullable|numeric',
            'bank_code' => 'nullable|string|max:10',
            'active' => 'boolean'
        ]);

        // Se active não foi enviado, definir como true por padrão
        $validated['active'] = $request->has('active') ? true : false;

        $account = BankAccount::create($validated);

        // Determinar qual ação realizar baseado no botão clicado
        $action = $request->get('action', 'save');
        
        if ($action === 'save_new') {
            return redirect()->route('bank-accounts.create')
                ->with('success', 'Conta bancária criada com sucesso! Você pode adicionar outra conta.');
        }

        return redirect()->route('bank-accounts.show', $account)
            ->with('success', 'Conta bancária criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        // Carregar transações recentes
        $recentTransactions = $bankAccount->transactions()
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        // Estatísticas da conta
        $stats = [
            'total_credit' => $bankAccount->transactions()->where('type', 'credit')->sum('amount') ?? 0,
            'total_debit' => $bankAccount->transactions()->where('type', 'debit')->sum('amount') ?? 0,
            'pending_count' => $bankAccount->transactions()->where('status', 'pending')->count(),
            'reconciled_count' => $bankAccount->transactions()->where('status', 'reconciled')->count(),
        ];

        // Estatísticas mensais
        $monthlyStats = [
            'transactions_count' => $bankAccount->transactions()
                ->whereMonth('transaction_date', now()->month)
                ->count(),
            'total_credit' => $bankAccount->transactions()
                ->where('type', 'credit')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount') ?? 0,
            'total_debit' => $bankAccount->transactions()
                ->where('type', 'debit')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount') ?? 0,
        ];

        // Categorias mais usadas nesta conta
        $topCategories = \App\Models\Category::whereHas('transactions', function($query) use ($bankAccount) {
                $query->where('bank_account_id', $bankAccount->id);
            })
            ->withCount(['transactions' => function($query) use ($bankAccount) {
                $query->where('bank_account_id', $bankAccount->id);
            }])
            ->withSum(['transactions' => function($query) use ($bankAccount) {
                $query->where('bank_account_id', $bankAccount->id);
            }], 'amount')
            ->orderByDesc('transactions_count')
            ->limit(5)
            ->get();

        // Todas as categorias para o modal de nova transação
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('bank-accounts.show', compact(
            'bankAccount', 
            'stats', 
            'monthlyStats',
            'recentTransactions',
            'topCategories',
            'categories'
        ));
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
            'type' => 'required|in:checking,savings,investment,credit_card',
            'balance' => 'nullable|numeric',
            'bank_code' => 'nullable|string|max:10',
            'active' => 'boolean',
        ]);

        // Se active não foi enviado, manter valor atual
        if (!$request->has('active')) {
            unset($validated['active']);
        }

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