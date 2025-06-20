<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\Client;
use App\Helpers\CategoryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountReceivableController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountReceivable::with('client');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Ordenação
        $sortBy = $request->get('sort', 'due_date');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $accountReceivables = $query->paginate(15);

        // Dados para filtros
        $clients = Client::active()->orderBy('name')->get();

        // Estatísticas
        $stats = [
            'total' => AccountReceivable::count(),
            'pending' => AccountReceivable::where('status', 'pending')->count(),
            'overdue' => AccountReceivable::where('status', 'overdue')->count(),
            'received' => AccountReceivable::where('status', 'received')->count(),
            'pending_amount' => AccountReceivable::where('status', 'pending')->sum('amount'),
            'overdue_amount' => AccountReceivable::where('status', 'overdue')->sum('amount'),
        ];

        return view('account-receivables.index', compact('accountReceivables', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::active()->orderBy('name')->get();
        $categories = CategoryHelper::getReceivableCategories();
        return view('account-receivables.create', compact('clients', 'categories'));
    }

    public function store(Request $request)
    {
        $validCategories = array_keys(CategoryHelper::getReceivableCategories());
        
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        AccountReceivable::create($request->all());

        return redirect()->route('account-receivables.index')
            ->with('success', 'Conta a receber cadastrada com sucesso!');
    }

    public function show(AccountReceivable $accountReceivable)
    {
        $accountReceivable->load('client');
        return view('account-receivables.show', compact('accountReceivable'));
    }

    public function edit(AccountReceivable $accountReceivable)
    {
        $clients = Client::active()->orderBy('name')->get();
        $categories = CategoryHelper::getReceivableCategories();
        return view('account-receivables.edit', compact('accountReceivable', 'clients', 'categories'));
    }

    public function update(Request $request, AccountReceivable $accountReceivable)
    {
        $validCategories = array_keys(CategoryHelper::getReceivableCategories());
        
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'category' => 'required|in:' . implode(',', $validCategories),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $accountReceivable->update($request->all());

        return redirect()->route('account-receivables.show', $accountReceivable)
            ->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(AccountReceivable $accountReceivable)
    {
        try {
            $accountReceivable->delete();
            return redirect()->route('account-receivables.index')
                ->with('success', 'Conta removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('account-receivables.index')
                ->with('error', 'Erro ao remover conta.');
        }
    }

    public function markAsReceived(AccountReceivable $accountReceivable)
    {
        $accountReceivable->update([
            'status' => 'received',
            'payment_date' => now(),
            'received_amount' => $accountReceivable->amount
        ]);

        return redirect()->back()
            ->with('success', 'Conta marcada como recebida!');
    }

    public function partialReceive(Request $request, AccountReceivable $accountReceivable)
    {
        $validator = Validator::make($request->all(), [
            'received_amount' => 'required|numeric|min:0.01|max:' . $accountReceivable->remaining_amount,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $newReceivedAmount = $accountReceivable->received_amount + $request->received_amount;
        $status = $newReceivedAmount >= $accountReceivable->amount ? 'received' : 'partial';

        $accountReceivable->update([
            'received_amount' => $newReceivedAmount,
            'status' => $status,
            'payment_date' => $status === 'received' ? now() : $accountReceivable->payment_date
        ]);

        return redirect()->back()
            ->with('success', 'Recebimento parcial registrado com sucesso!');
    }

    public function overdue()
    {
        $accountReceivables = AccountReceivable::with('client')
            ->overdue()
            ->orderBy('due_date')
            ->paginate(15);

        return view('account-receivables.overdue', compact('accountReceivables'));
    }
}
