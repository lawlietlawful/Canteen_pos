<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with date, status, and cashier filters
     */
    public function index(Request $request): View
    {
        $cashiers = User::orderBy('name')->get(['idno', 'name']);

        $query = Order::with(['user', 'payment', 'orderItems.product'])
            ->withCount('orderItems');

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('cashier') && $request->cashier !== 'all') {
            $query->where('idno', $request->cashier);
        }

        $orders = $query->orderBy('order_date', 'desc')
            ->orderBy('order_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', compact('orders', 'cashiers'));
    }

    /**
     * Show order details
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'payment', 'orderItems.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Cancel an order and restore product stock within a transaction
     */
    public function cancel(Order $order): RedirectResponse
    {
        try {
            $restoredCount = 0;

            DB::transaction(function () use ($order, &$restoredCount) {
                // Lock order for update
                $lockedOrder = Order::where('order_id', $order->order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedOrder->status === 'cancelled') {
                    throw new Exception("Order #ORD-" . str_pad($order->order_id, 5, '0', STR_PAD_LEFT) . " has already been cancelled.");
                }

                // Restore stock for all order items with pessimistic locking
                foreach ($lockedOrder->orderItems as $item) {
                    $product = Product::where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($product) {
                        $product->stock_qty += $item->quantity;
                        // If product was previously unavailable due to 0 stock, re-enable it
                        if ($product->stock_qty > 0 && !$product->is_available) {
                            $product->is_available = true;
                        }
                        $product->save();
                        $restoredCount += $item->quantity;
                    }
                }

                // Update status to 'cancelled'
                $lockedOrder->update([
                    'status' => 'cancelled',
                ]);
            });

            return back()->with('success', "Order #ORD-" . str_pad($order->order_id, 5, '0', STR_PAD_LEFT) . " was successfully cancelled. {$restoredCount} item unit(s) were restored to inventory.");

        } catch (Exception $e) {
            return back()->with('danger', "Failed to cancel order: " . $e->getMessage());
        }
    }
}

