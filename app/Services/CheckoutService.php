<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Execute POS order checkout inside a single atomic database transaction.
     *
     * @param  array   $items        Array of ['product_id' => int, 'quantity' => int]
     * @param  string  $paymentMethod 'cash' or 'gcash'
     * @param  float   $amountPaid   Amount tendered
     * @param  string  $cashierIdno  Cashier's idno from app1.users
     * @param  string|null $customerName Optional customer name
     * @return Order
     * @throws Exception
     */
    public function checkout(
        array $items,
        string $paymentMethod,
        float $amountPaid,
        string $cashierIdno,
        ?string $customerName = null
    ): Order {
        if (empty($items)) {
            throw new Exception("Cart is empty. Please add products before checking out.");
        }

        $paymentMethod = strtolower(trim($paymentMethod));
        if (!in_array($paymentMethod, ['cash', 'gcash'], true)) {
            throw new Exception("Invalid payment method. Only Cash and GCash are accepted.");
        }

        return DB::transaction(function () use ($items, $paymentMethod, $amountPaid, $cashierIdno, $customerName) {
            $verifiedItems = [];
            $totalAmount = 0.0;

            // (a) Re-read each product with lockForUpdate() - never trust prices from browser!
            foreach ($items as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);

                if ($productId <= 0 || $qty <= 0) {
                    throw new Exception("Invalid item or quantity in order payload.");
                }

                $product = Product::where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new Exception("Product ID #{$productId} not found in database.");
                }

                if (!$product->is_available) {
                    throw new Exception("Product '{$product->product_name}' is currently marked as unavailable.");
                }

                if ($product->stock_qty < $qty) {
                    throw new Exception("Insufficient stock for '{$product->product_name}'. Available: {$product->stock_qty}, Requested: {$qty}.");
                }

                $unitPrice = (float)$product->price;
                $lineTotal = round($unitPrice * $qty, 2);
                $totalAmount += $lineTotal;

                $verifiedItems[] = [
                    'product'    => $product,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                ];
            }

            $totalAmount = round($totalAmount, 2);

            // Validate Payment Calculations
            $finalAmountPaid = 0.0;
            $changeAmount = 0.0;

            if ($paymentMethod === 'cash') {
                if ($amountPaid < $totalAmount) {
                    throw new Exception("Amount tendered (₱" . number_format($amountPaid, 2) . ") is less than the total required (₱" . number_format($totalAmount, 2) . ").");
                }
                $finalAmountPaid = round($amountPaid, 2);
                $changeAmount = round($finalAmountPaid - $totalAmount, 2);
            } else {
                // GCash: Exact amount paid, zero change
                $finalAmountPaid = $totalAmount;
                $changeAmount = 0.00;
            }

            // (b) Create Order record
            $order = Order::create([
                'idno'          => $cashierIdno,
                'customer_name' => trim($customerName ?: 'Walk-in Customer'),
                'order_date'    => now(),
                'status'        => 'paid',
                'total_amount'  => $totalAmount,
            ]);

            // (c) Create OrderItems and Deduct Inventory Stock
            foreach ($verifiedItems as $v) {
                // Note: line_total is a PostgreSQL generated column; never insert it!
                OrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $v['product']->product_id,
                    'quantity'   => $v['quantity'],
                    'unit_price' => $v['unit_price'],
                ]);

                // Deduct stock
                $v['product']->decrement('stock_qty', $v['quantity']);
            }

            // (d) Create Payment record
            Payment::create([
                'order_id'       => $order->order_id,
                'payment_method' => $paymentMethod,
                'amount_paid'    => $finalAmountPaid,
                'change_amount'  => $changeAmount,
                'paid_at'        => now(),
            ]);

            return $order;
        });
    }
}

