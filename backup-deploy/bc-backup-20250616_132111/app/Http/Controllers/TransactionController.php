<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Query básica com relacionamentos
            $query = Transaction::with(['bankAccount']);

            // Filtros básicos
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('transaction_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('transaction_date', '<=', $request->date_to);
            }

            if ($request->filled('bank_account_id') && $request->bank_account_id !== 'all') {
                $query->where('bank_account_id', $request->bank_account_id);
            }

            // Ordenação
            $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

            // Paginação
            $transactions = $query->paginate(15);

            // Estatísticas básicas
            $stats = [
                'total_transactions' => Transaction::count(),
                'total_credit' => Transaction::where('type', 'credit')->sum('amount') ?? 0,
                'total_debit' => Transaction::where('type', 'debit')->sum('amount') ?? 0,
                'balance' => (Transaction::where('type', 'credit')->sum('amount') ?? 0) - (Transaction::where('type', 'debit')->sum('amount') ?? 0)
            ];

            // Dados para filtros
            $bankAccounts = BankAccount::orderBy('name')->get();
            $categories = Category::orderBy('name')->get();

            // Resposta AJAX
            if ($request->ajax()) {
                if ($request->has('table_only')) {
                    return view('transactions.partials.table', compact('transactions'))->render();
                }
                
                return response()->json([
                    'html' => view('transactions.partials.table', compact('transactions'))->render(),
                    'stats' => $stats,
                    'pagination' => $transactions->links()->render()
                ]);
            }

            return view('transactions.index', compact('transactions', 'bankAccounts', 'categories', 'stats'));
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            
            return view('transactions.index', [
                'transactions' => collect([]),
                'bankAccounts' => collect([]),
                'categories' => collect([]),
                'stats' => ['total_transactions' => 0, 'total_credit' => 0, 'total_debit' => 0, 'balance' => 0]
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            // Dados para o formulário
            $bankAccounts = BankAccount::where('active', true)->orderBy('name')->get();
            $categories = Category::orderBy('name')->get();
            
            // Se for duplicação de transação
            $duplicateTransaction = null;
            if ($request->has('duplicate')) {
                $duplicateTransaction = Transaction::find($request->get('duplicate'));
            }
            
            return view('transactions.create', compact('bankAccounts', 'categories', 'duplicateTransaction'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
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
            'type' => 'required|in:income,expense',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string'
        ]);

        try {
            // Ajustar o valor baseado no tipo
            if ($validated['type'] === 'expense') {
                $validated['amount'] = -abs($validated['amount']);
            } else {
                $validated['amount'] = abs($validated['amount']);
            }

            // Processar tags
            if (!empty($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
                $validated['tags'] = json_encode($validated['tags']);
            } else {
                $validated['tags'] = null;
            }

            $transaction = Transaction::create($validated);

            // Atualizar saldo da conta
            $this->updateAccountBalance($transaction->bank_account_id);

            return redirect()->route('transactions.index')
                           ->with('success', 'Transação criada com sucesso!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'Erro ao criar transação: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Gerar hash para evitar transações duplicadas
     */
    private function generateImportHash($data)
    {
        $hashData = [
            'date' => $data['transaction_date'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'account' => $data['bank_account_id']
        ];
        
        return md5(json_encode($hashData));
    }

    /**
     * Atualizar saldo da conta
     */
    private function updateAccountBalance($account_id)
    {
        try {
            $account = BankAccount::find($account_id);
            if ($account) {
                $balance = Transaction::where('bank_account_id', $account_id)->sum('amount');
                $account->balance = $balance;
                $account->save();
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar saldo da conta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        try {
            // Carregar relacionamentos necessários
            $transaction->load(['bankAccount', 'category', 'reconciliation']);
            
            // Garantir que os campos essenciais existem
            if (!$transaction->transaction_date) {
                $transaction->transaction_date = $transaction->created_at;
            }
            
            return view('transactions.show', compact('transaction'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Erro ao carregar transação: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        try {
            // Verificar se a transação pode ser editada
            if (isset($transaction->status) && $transaction->status === 'reconciled') {
                return redirect()->route('transactions.show', $transaction)
                               ->with('warning', 'Transações conciliadas requerem atenção especial ao editar.');
            }
            
            // Carregar dados necessários para o formulário
            $bankAccounts = BankAccount::where('active', true)->orderBy('name')->get();
            $categories = Category::orderBy('name')->get();
            
            // Carregar relacionamentos
            $transaction->load(['bankAccount', 'category']);
            
            return view('transactions.edit', compact('transaction', 'bankAccounts', 'categories'));
            
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')
                           ->with('error', 'Erro ao carregar formulário de edição: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        try {
            // Validação
            $validatedData = $request->validate([
                'transaction_date' => 'required|date|before_or_equal:now',
                'bank_account_id' => 'required|exists:bank_accounts,id',
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'type' => 'required|in:credit,debit',
                'category_id' => 'nullable|exists:categories,id',
                'reference_number' => 'nullable|string|max:100',
                'status' => 'nullable|in:pending,reconciled,cancelled'
            ]);

            // Verificar se a conta bancária existe e está ativa
            $bankAccount = BankAccount::findOrFail($validatedData['bank_account_id']);
            if (!$bankAccount->active) {
                return back()->withErrors(['bank_account_id' => 'A conta bancária selecionada não está ativa.']);
            }

            // Backup do valor anterior para ajustar saldo se necessário
            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;
            $oldAccountId = $transaction->bank_account_id;

            // Atualizar a transação
            $transaction->update($validatedData);

            // Ajustar saldo da conta se necessário
            if ($oldAccountId != $validatedData['bank_account_id'] || 
                $oldAmount != $validatedData['amount'] || 
                $oldType != $validatedData['type']) {
                
                $this->adjustAccountBalance($transaction, $oldAmount, $oldType, $oldAccountId);
            }

            return redirect()->route('transactions.show', $transaction)
                           ->with('success', 'Transação atualizada com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar transação: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Ajustar saldo da conta após edição
     */
    private function adjustAccountBalance(Transaction $transaction, $oldAmount, $oldType, $oldAccountId)
    {
        try {
            // Reverter o efeito da transação anterior
            if ($oldAccountId) {
                $oldAccount = BankAccount::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldType === 'credit') {
                        $oldAccount->balance -= $oldAmount;
                    } else {
                        $oldAccount->balance += $oldAmount;
                    }
                    $oldAccount->save();
                }
            }
            
            // Aplicar o efeito da nova transação
            $newAccount = BankAccount::find($transaction->bank_account_id);
            if ($newAccount) {
                if ($transaction->type === 'credit') {
                    $newAccount->balance += $transaction->amount;
                } else {
                    $newAccount->balance -= $transaction->amount;
                }
                $newAccount->save();
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao ajustar saldo da conta: ' . $e->getMessage());
        }
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
     * Quick update for inline editing
     */
    public function quickUpdate(Request $request, Transaction $transaction)
    {
        $field = $request->field;
        $value = $request->value;

        // Validar campo permitido
        $allowedFields = ['description', 'amount', 'category_id', 'date', 'status'];
        if (!in_array($field, $allowedFields)) {
            return response()->json(['message' => 'Campo não permitido'], 400);
        }

        // Validações específicas por campo
        $rules = [];
        switch ($field) {
            case 'amount':
                $rules = ['value' => 'required|numeric'];
                break;
            case 'category_id':
                $rules = ['value' => 'nullable|exists:categories,id'];
                break;
            case 'date':
                $rules = ['value' => 'required|date'];
                $field = 'transaction_date';
                break;
            case 'status':
                $rules = ['value' => 'required|in:pending,completed,cancelled'];
                break;
            default:
                $rules = ['value' => 'required|string|max:255'];
        }

        $request->validate($rules);

        // Atualizar transação
        $transaction->update([$field => $value]);

        // Recarregar com relacionamentos
        $transaction->load('bankAccount');

        return response()->json([
            'success' => true,
            'message' => 'Transação atualizada com sucesso',
            'data' => $transaction
        ]);
    }

    /**
     * Bulk delete transactions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id'
        ]);

        $count = Transaction::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} transação(ões) excluída(s) com sucesso",
            'deleted' => $count
        ]);
    }

    /**
     * Bulk categorize transactions
     */
    public function bulkCategorize(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $count = Transaction::whereIn('id', $request->ids)
            ->update(['category_id' => $request->category_id]);

        return response()->json([
            'success' => true,
            'message' => "{$count} transação(ões) categorizada(s) com sucesso",
            'updated' => $count
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $count = Transaction::whereIn('id', $request->ids)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => "{$count} transação(ões) atualizadas com sucesso",
            'updated' => $count
        ]);
    }

    /**
     * Auto-categorize transactions
     */
    public function autoCategorize(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id'
        ]);

        $transactions = Transaction::whereIn('id', $request->ids)->get();
        $categorized = 0;

        foreach ($transactions as $transaction) {
            // Lógica simples de auto-categorização baseada na descrição
            $description = strtolower($transaction->description);
            $category = null;

            // Exemplos de regras de categorização
            if (str_contains($description, 'supermercado') || str_contains($description, 'mercado')) {
                $category = Category::where('name', 'like', '%alimentação%')->first();
            } elseif (str_contains($description, 'posto') || str_contains($description, 'combustivel')) {
                $category = Category::where('name', 'like', '%transporte%')->first();
            } elseif (str_contains($description, 'farmacia') || str_contains($description, 'medic')) {
                $category = Category::where('name', 'like', '%saúde%')->first();
            } elseif (str_contains($description, 'restaurante') || str_contains($description, 'lanchonete')) {
                $category = Category::where('name', 'like', '%alimentação%')->first();
            }

            if ($category && !$transaction->category_id) {
                $transaction->update(['category_id' => $category->id]);
                $categorized++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$categorized} transação(ões) categorizadas automaticamente",
            'categorized' => $categorized
        ]);
    }

    /**
     * Export transactions
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['bankAccount', 'category']);

        // Aplicar mesmos filtros do index
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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

        // Se IDs específicos foram fornecidos
        if ($request->filled('ids')) {
            $query->whereIn('id', $request->ids);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        // Gerar CSV
        $filename = 'transacoes_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalhos
            fputcsv($file, [
                'Data',
                'Descrição',
                'Categoria',
                'Conta',
                'Valor',
                'Tipo',
                'Status',
                'Referência'
            ]);

            // Dados
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '',
                    $transaction->description,
                    $transaction->category ? $transaction->category->name : 'Sem categoria',
                    $transaction->bankAccount ? $transaction->bankAccount->name : 'N/A',
                    $transaction->amount,
                    $transaction->type === 'credit' ? 'Crédito' : 'Débito',
                    ucfirst($transaction->status ?? 'indefinido'),
                    $transaction->reference_number ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calcular estatísticas das transações
     */
    private function calculateStats($request)
    {
        $query = Transaction::query();
        
        // Aplicar mesmos filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('bank_account_id') && $request->bank_account_id !== 'all') {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Calcular estatísticas
        $totalTransactions = $query->count();
        $totalCredit = $query->where('type', 'credit')->sum('amount') ?? 0;
        $totalDebit = $query->where('type', 'debit')->sum('amount') ?? 0;
        $balance = $totalCredit - $totalDebit;

        return [
            'total_transactions' => $totalTransactions,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'balance' => $balance,
            'avg_transaction' => $totalTransactions > 0 ? ($totalCredit + $totalDebit) / $totalTransactions : 0
        ];
    }

    /**
     * Analytics mensal
     */
    private function getMonthlyAnalytics()
    {
        $monthlyData = Transaction::selectRaw('
            YEAR(transaction_date) as year,
            MONTH(transaction_date) as month,
            type,
            SUM(amount) as total,
            COUNT(*) as count
        ')
        ->whereNotNull('transaction_date')
        ->where('transaction_date', '>=', now()->subMonths(12))
        ->groupBy('year', 'month', 'type')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        return $monthlyData->groupBy(function($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        });
    }
}