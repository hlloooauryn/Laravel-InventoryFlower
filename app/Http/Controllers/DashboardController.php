<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalValuation = Product::sum(DB::raw('price * stock'));

        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyIn = StockMovement::where('type', 'IN')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('quantity');

        $monthlyOut = StockMovement::where('type', 'OUT')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('quantity');

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        $recentTransactions = StockMovement::with(['product', 'user.profile'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalValuation',
            'lowStockCount',
            'monthlyIn',
            'monthlyOut',
            'lowStockProducts',
            'recentTransactions'
        ));
    }
}
