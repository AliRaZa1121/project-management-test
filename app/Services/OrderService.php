<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Generate a unique sequential order number.
     */
    public function generateOrderNumber(): string
    {
        $lastOrder = Order::latest('id')->first();
        $nextNumber = $lastOrder ? ((int) substr($lastOrder->order_number, -6)) + 1 : 100001;
        return 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new order or update an existing order.
     */
    public function createOrderOrUpdate(array $data, $orderId = null): Order
    {
        return DB::transaction(function () use ($data, $orderId) {

            if ($orderId) {
                $order = Order::findOrFail($orderId);
                if ($order->status !== 'pending') {
                    throw new \Exception('Only pending orders can be updated.');
                }
            } else {
                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'total_amount' => 0,
                    'status' => 'pending',
                ]);
            }

            $totalAmount = 0;

            foreach ($data['items'] as $item) {

                $total = $item['quantity'] * $item['unit_price'];

                $orderItem = $order->items()->firstOrNew(['product_name' => $item['product_name']]);
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['unit_price'];
                $orderItem->total_price = $total;
                $orderItem->save();
                $totalAmount += $total;
            }

            $order->update(['total_amount' => $totalAmount]);

            if ($totalAmount <= 1000 && $order->status !== 'approved') {
                $order->update(['status' => 'approved', 'approved_at' => now()]);
            }

            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $totalAmount <= 1000 ? 'approved' : 'pending',
                'changed_at' => now(),
            ]);

            return $order;
        });
    }



    /**
     * Approve or reject an order.
     */
    public function processApproval(Order $order, string $status, int $userId, ?string $comment = null)
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Only pending orders can be approved or rejected.');
        }

        if ($status === 'approved' && $order->total_amount <= 1000) {
            throw new \Exception('Orders above $1000 require approval.');
        }

        return DB::transaction(function () use ($order, $status, $userId, $comment) {
            $order->update([
                'status' => $status,
                'approved_at' => $status === 'approved' ? now() : null,
            ]);

            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'changed_at' => now(),
            ]);

            return $order;
        });
    }


    public function listOrders(array $filters): LengthAwarePaginator
    {
        $query = Order::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['order_number'] ?? null, fn($q, $order_number) => $q->where('order_number', 'like', "$order_number%"))
            ->when($filters['created_at'] ?? null, fn($q, $created_at) => $q->whereDate('created_at', $created_at))
            ->when($filters['total_amount_min'] ?? null, fn($q, $total_amount_min) => $q->where('total_amount', '>=', $total_amount_min))
            ->when($filters['total_amount_max'] ?? null, fn($q, $total_amount_max) => $q->where('total_amount', '<=', $total_amount_max));

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
