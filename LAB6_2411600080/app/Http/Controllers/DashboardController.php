<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->recent()
            ->take(8)
            ->get();

        // Category summary for charts / display
        $categorySummary = Product::select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity * unit_price) as value'))
            ->groupBy('category')
            ->orderByDesc('value')
            ->get();

        $unreadNotifications = Auth::user()->unreadNotifications()->take(5)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalInventoryValue',
            'recentProducts',
            'lowStockProducts',
            'recentTransactions',
            'categorySummary',
            'unreadNotifications',
            'unreadCount'
        ));
    }
}
