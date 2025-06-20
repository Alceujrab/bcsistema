<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }

        $clients = $query->orderBy('name')->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Client::count(),
            'active' => Client::where('active', true)->count(),
            'inactive' => Client::where('active', false)->count(),
        ];

        return view('clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|in:cpf,cnpj',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Client::create($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Client $client)
    {
        $client->load('accountReceivables');
        
        // Estatísticas do cliente
        $stats = [
            'total_receivables' => $client->accountReceivables->count(),
            'pending_amount' => $client->accountReceivables->where('status', 'pending')->sum('remaining_amount'),
            'overdue_count' => $client->accountReceivables->where('status', 'overdue')->count(),
        ];

        return view('clients.show', compact('client', 'stats'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'document_type' => 'nullable|in:cpf,cnpj',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client->update($request->all());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return redirect()->route('clients.index')
                ->with('success', 'Cliente removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('clients.index')
                ->with('error', 'Erro ao remover cliente. Verifique se não há contas a receber associadas.');
        }
    }

    public function toggleStatus(Client $client)
    {
        $client->update(['active' => !$client->active]);
        
        $status = $client->active ? 'ativado' : 'desativado';
        return redirect()->back()
            ->with('success', "Cliente {$status} com sucesso!");
    }
}
