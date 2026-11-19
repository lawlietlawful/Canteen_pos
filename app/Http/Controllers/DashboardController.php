<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main management dashboard
     */
    public function index(): View
    {
        // 1. Today's sales total and orders count
        $today = Carbon::today('Asia/Manila')->toDateString();

        $todayStats = Order::where('status', 'paid')
            ->whereDate('order_date', $today)
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_sales, COUNT(*) as order_count')
            ->first();

        $todaySales = (float)($todayStats->total_sales ?? 0);
        $todayOrders = (int)($todayStats->order_count ?? 0);

        // 2. Active products and low stock count
        $activeProducts = Product::where('is_available', true)->count();
        $lowStockCount = Product::where('stock_qty', '<=', 10)->count();

        // 3. Low-stock list (stock_qty <= 10)
        $lowStockProducts = Product::with('category')
            ->where('stock_qty', '<=', 10)
            ->orderBy('stock_qty', 'asc')
            ->orderBy('product_name', 'asc')
            ->take(10)
            ->get();

        // 4. Top 5 selling products (by total quantity sold)
        $topProducts = DB::table('canteen_pos.order_items as oi')
            ->join('canteen_pos.orders as o', 'oi.order_id', '=', 'o.order_id')
            ->join('canteen_pos.products as p', 'oi.product_id', '=', 'p.product_id')
            ->leftJoin('canteen_pos.categories as c', 'p.category_id', '=', 'c.category_id')
            ->where('o.status', 'paid')
            ->select(
                'p.product_id',
                'p.product_name',
                DB::raw("COALESCE(c.category_name, 'General') as category_name"),
                DB::raw('SUM(oi.quantity) as total_sold'),
                DB::raw('SUM(oi.quantity * oi.unit_price) as total_revenue')
            )
            ->groupBy('p.product_id', 'p.product_name', 'c.category_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 5. Sales for the last 7 days (continuous series)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Manila')->subDays($i);
            $dateStr = $date->toDateString();
            $label = $date->format('M d');

            $dayStats = Order::where('status', 'paid')
                ->whereDate('order_date', $dateStr)
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total, COUNT(*) as count')
                ->first();

            $last7Days[] = [
                'date'        => $dateStr,
                'label'       => $label,
                'sales'       => (float)($dayStats->total ?? 0),
                'order_count' => (int)($dayStats->count ?? 0),
            ];
        }

        // 6. Payment breakdown (Cash vs GCash)
        $paymentStats = DB::table('canteen_pos.payments as p')
            ->join('canteen_pos.orders as o', 'p.order_id', '=', 'o.order_id')
            ->where('o.status', 'paid')
            ->select(
                'p.payment_method',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(o.total_amount), 0) as total_amount')
            )
            ->groupBy('p.payment_method')
            ->get()
            ->keyBy('payment_method');

        $cashSales = (float)($paymentStats->get('cash')->total_amount ?? 0);
        $gcashSales = (float)($paymentStats->get('gcash')->total_amount ?? 0);
        $cashOrders = (int)($paymentStats->get('cash')->total_orders ?? 0);
        $gcashOrders = (int)($paymentStats->get('gcash')->total_orders ?? 0);

        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'activeProducts',
            'lowStockCount',
            'lowStockProducts',
            'topProducts',
            'last7Days',
            'cashSales',
            'gcashSales',
            'cashOrders',
            'gcashOrders'
        ));
    }
}

