<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /**
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->count();
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $totalInventoryValue = Product::select(DB::raw('SUM(quantity * unit_price) as total'))->value('total') ?? 0;
        $recentTransactionsCount = InventoryTransaction::where('created_at', '>=', now()->subDay())->count();

        return response()->json([
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_inventory_value' => round((float) $totalInventoryValue, 2),
            'recent_transactions_count' => $recentTransactionsCount,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/products/low-stock
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->take(10)
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level', 'unit_price']);

        return response()->json($products);
    }

    /**
     * GET /api/transactions/recent
     */
    public function recentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product:id,name,sku', 'user:id,name'])
            ->recent()
            ->take(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'type_label' => $t->type_label,
                    'quantity' => $t->quantity,
                    'signed_quantity' => $t->signed_quantity,
                    'product_name' => $t->product->name ?? 'N/A',
                    'product_sku' => $t->product->sku ?? '',
                    'user_name' => $t->user->name ?? 'N/A',
                    'reference_number' => $t->reference_number,
                    'created_at' => $t->created_at->toDateTimeString(),
                    'created_at_human' => $t->created_at->diffForHumans(),
                ];
            });

        return response()->json($transactions);
    }
}
