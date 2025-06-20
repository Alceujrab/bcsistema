<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reconciliations = Reconciliation::with('bankAccount', 'creator')
            ->latest()
            ->paginate(20);

        return view('reconciliations.index', compact('reconciliations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bankAccounts = BankAccount::where('active', true)->get();
        
        $bankAccountId = $request->input('bank_account_id');
        $lastReconciliation = null;
        
        if ($bankAccountId) {
            $lastReconciliation = Reconciliation::where('bank_account_id', $bankAccountId)
                ->where('status', 'approved')
                ->latest('end_date')
                ->first();
        }

        return view('reconciliations.create', compact('bankAccounts', 'lastReconciliation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'starting_balance' => 'required|numeric',
            'ending_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id() ?? 1; // Usar ID 1 se não houver autenticação

        DB::transaction(function () use ($validated, &$reconciliation) {
            $reconciliation = Reconciliation::create($validated);
            
            // Associar transações do período
            Transaction::where('bank_account_id', $validated['bank_account_id'])
                ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']])
                ->where('status', 'pending')
                ->update(['reconciliation_id' => $reconciliation->id]);
            
            $reconciliation->calculate();
        });

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reconciliation $reconciliation)
    {
        $reconciliation->load([
            'bankAccount',
            'transactions' => function ($query) {
                $query->orderBy('transaction_date')->orderBy('id');
            },
            'creator',
            'approver'
        ]);

        $summary = [
            'total_credits' => $reconciliation->transactions()->where('type', 'credit')->sum('amount'),
            'total_debits' => $reconciliation->transactions()->where('type', 'debit')->sum('amount'),
            'transaction_count' => $reconciliation->transactions()->count(),
        ];

        return view('reconciliations.show', compact('reconciliation', 'summary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return redirect()->route('reconciliations.show', $reconciliation)
                ->with('error', 'Apenas conciliações em rascunho podem ser editadas.');
        }

        $bankAccounts = BankAccount::where('active', true)->get();
        return view('reconciliations.edit', compact('reconciliation', 'bankAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return back()->with('error', 'Apenas conciliações em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'starting_balance' => 'required|numeric',
            'ending_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $reconciliation->update($validated);
        $reconciliation->calculate();

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reconciliation $reconciliation)
    {
        if ($reconciliation->status === 'approved') {
            return back()->with('error', 'Não é possível excluir conciliações aprovadas.');
        }

        // Desassociar transações
        $reconciliation->transactions()->update(['reconciliation_id' => null]);
        
        $reconciliation->delete();

        return redirect()->route('reconciliations.index')
            ->with('success', 'Conciliação excluída com sucesso!');
    }

    /**
     * Process reconciliation
     */
    public function process(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'draft') {
            return back()->with('error', 'Esta conciliação já foi processada.');
        }

        DB::transaction(function () use ($reconciliation) {
            // Marcar transações como conciliadas
            $reconciliation->transactions()
                ->where('status', 'pending')
                ->update(['status' => 'reconciled']);
            
            // Recalcular e atualizar status
            $reconciliation->calculate();
            $reconciliation->update(['status' => 'completed']);
            
            // Atualizar saldo da conta
            $reconciliation->bankAccount->updateBalance();
        });

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação processada com sucesso!');
    }

    /**
     * Approve reconciliation
     */
    public function approve(Reconciliation $reconciliation)
    {
        if ($reconciliation->status !== 'completed') {
            return back()->with('error', 'Apenas conciliações completas podem ser aprovadas.');
        }

        if (!$reconciliation->isBalanced()) {
            return back()->with('error', 'A conciliação possui diferenças e não pode ser aprovada.');
        }

        $reconciliation->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);

        return redirect()->route('reconciliations.show', $reconciliation)
            ->with('success', 'Conciliação aprovada com sucesso!');
    }

    /**
     * Generate reconciliation report
     */
    public function report(Reconciliation $reconciliation)
    {
        $reconciliation->load(['bankAccount', 'transactions', 'creator', 'approver']);
        
        return view('reconciliations.report', compact('reconciliation'));
    }
}