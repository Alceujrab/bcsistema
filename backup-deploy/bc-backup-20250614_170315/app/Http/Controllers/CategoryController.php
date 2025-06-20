<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('transactions')
            ->withSum('transactions', 'amount')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(); // Mudado para get() para evitar erro de paginação
            
        // Estatísticas
        $stats = [
            'total_categories' => $categories->count(),
            'active_categories' => $categories->where('active', true)->count(),
            'used_categories' => $categories->where('transactions_count', '>', 0)->count(),
            'unused_categories' => $categories->where('transactions_count', 0)->count(),
        ];
        
        return view('categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:7',
            'keywords' => 'nullable|string',
            'type' => 'required|in:income,expense,both',
            'active' => 'boolean',
        ]);

        if (!empty($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        // Carregar estatísticas da categoria
        $category->loadCount('transactions')
            ->loadSum('transactions', 'amount');
            
        // Transações recentes desta categoria
        $recentTransactions = Transaction::where('category', $category->name)
            ->with('bankAccount')
            ->latest('transaction_date')
            ->limit(5)
            ->get();
            
        // Estatísticas
        $categoryTransactions = Transaction::where('category', $category->name);
        $stats = [
            'total_transactions' => $categoryTransactions->count(),
            'total_amount' => $categoryTransactions->sum('amount') ?? 0,
            'credit_amount' => $categoryTransactions->where('type', 'credit')->sum('amount') ?? 0,
            'debit_amount' => $categoryTransactions->where('type', 'debit')->sum('amount') ?? 0,
        ];
        
        return view('categories.edit', compact('category', 'recentTransactions', 'stats'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:7',
            'keywords' => 'nullable|string',
            'type' => 'required|in:income,expense,both',
            'active' => 'boolean',
        ]);

        if (!empty($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        } else {
            $validated['keywords'] = [];
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        if ($category->transactions()->count() > 0) {
            return back()->with('error', 'Não é possível excluir categoria com transações vinculadas.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    public function reorder(Request $request)
    {
        $positions = $request->input('positions', []);
        
        foreach ($positions as $position => $categoryId) {
            Category::where('id', $categoryId)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    public function show(Category $category)
    {
        // Transações recentes desta categoria
        $recentTransactions = Transaction::where('category', $category->name)
            ->with('bankAccount')
            ->latest('transaction_date')
            ->limit(10)
            ->get();
            
        // Estatísticas da categoria
        $categoryTransactions = Transaction::where('category', $category->name);
        $stats = [
            'total_transactions' => $categoryTransactions->count(),
            'total_amount' => $categoryTransactions->sum('amount') ?? 0,
            'credit_amount' => $categoryTransactions->where('type', 'credit')->sum('amount') ?? 0,
            'debit_amount' => $categoryTransactions->where('type', 'debit')->sum('amount') ?? 0,
        ];
        
        return view('categories.show', compact('category', 'recentTransactions', 'stats'));
    }
}
