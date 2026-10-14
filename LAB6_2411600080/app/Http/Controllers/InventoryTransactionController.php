<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of transactions with filters.
     */
    public function index(Request $request): View
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

        return view('transactions.index', compact('transactions', 'products'));
    }

    /**
     * Show the form for recording a new transaction.
     */
    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        $types = InventoryTransaction::types();

        return view('transactions.create', compact('products', 'types'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(array_keys(InventoryTransaction::types()))],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Extra stock check for stock_out (observer also checks)
        if ($validated['type'] === InventoryTransaction::TYPE_STOCK_OUT) {
            $product = Product::findOrFail($validated['product_id']);
            if ($validated['quantity'] > $product->quantity) {
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock. Available: {$product->quantity}."]);
            }
        }

        $validated['user_id'] = Auth::id();

        InventoryTransaction::create($validated);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction recorded successfully. Stock levels updated.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(InventoryTransaction $transaction): View
    {
        $transaction->load(['product', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Remove the specified transaction (reverses stock change via observer).
     */
    public function destroy(InventoryTransaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction deleted and stock levels reversed.');
    }

    /**
     * Show the stock adjustment form.
     */
    public function adjustmentForm(): View
    {
        $products = Product::orderBy('name')->get();
        return view('transactions.adjustment', compact('products'));
    }

    /**
     * Process a stock adjustment.
     */
    public function processAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $difference = $validated['physical_count'] - $product->quantity;

        if ($difference === 0) {
            return back()->with('info', 'Physical count matches system quantity. No adjustment needed.');
        }

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => InventoryTransaction::TYPE_ADJUSTMENT,
            'quantity' => $difference, // signed difference
            'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            'notes' => "Reason: {$validated['reason']}. " . ($validated['notes'] ?? '') .
                       " (System: {$product->quantity} → Physical: {$validated['physical_count']})",
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', "Stock adjusted by {$difference}. New quantity: {$validated['physical_count']}.");
    }
}
