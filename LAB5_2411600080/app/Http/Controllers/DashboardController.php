<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real inventory statistics.
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->count();
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $totalInventoryValue = Product::select(DB::raw('SUM(quantity * unit_price) as total'))->value('total') ?? 0;

        $recentProducts = Product::latest()->take(5)->get();
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->take(5)
            ->get();

        // Category summary for charts / display
        $categorySummary = Product::select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity * unit_price) as value'))
            ->groupBy('category')
            ->orderByDesc('value')
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalInventoryValue',
            'recentProducts',
            'lowStockProducts',
            'categorySummary'
        ));
    }
}
