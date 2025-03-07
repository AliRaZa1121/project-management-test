<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\OrderListing;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order.
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrderOrUpdate($request->validated());
        return ResponseHelper::success('Order created successfully', $order);
    }

    /**
     * List all orders with filters.
     */
    public function index(OrderListing $request): JsonResponse
    {
        $orders = $this->orderService->listOrders($request->validated());
        return ResponseHelper::success('Orders retrieved successfully', $orders);
    }

    /**
     * Show a specific order by its ID.
     */
    public function show(Order $order): JsonResponse
    {
        return ResponseHelper::success('Order retrieved successfully', $order);
    }

    /**
     * Update an order (only if pending).
     */
    public function update(Order $order, CreateOrderRequest $request): JsonResponse
    {
        if ($order->status !== 'pending') {
            return ResponseHelper::error('Only pending orders can be updated.', 400);
        }

        $updatedOrder = $this->orderService->createOrderOrUpdate($request->validated(), $order->id); // Assuming an update would follow a similar logic
        return ResponseHelper::success('Order updated successfully', $updatedOrder);
    }

    /**
     * Delete an order (only if pending).
     */
    public function destroy(Order $order): JsonResponse
    {
        if ($order->status !== 'pending') {
            return ResponseHelper::error('Only pending orders can be deleted.', 400);
        }

        $order->delete();
        return ResponseHelper::success('Order deleted successfully');
    }
}
