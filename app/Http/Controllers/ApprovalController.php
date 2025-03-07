<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ApprovalRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class ApprovalController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Process the approval for an order (approve or reject).
     */
    public function processApproval(Order $order, ApprovalRequest $request): JsonResponse
    {
        $approvedOrder = $this->orderService->processApproval(
            $order,
            $request->status,
            auth()->id(),
            $request->comment
        );

        return ResponseHelper::success('Order approval processed successfully', $approvedOrder);
    }
}
