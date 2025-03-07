<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderListing extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:pending,approved,rejected',
            'order_number' => 'nullable|string',
            'created_at' => 'nullable|date',
            'total_amount_min' => 'nullable|numeric|min:0',
            'total_amount_max' => 'nullable|numeric|min:0',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid order status.',
            'total_amount_min.min' => 'Minimum total amount must be at least 0.',
            'total_amount_max.min' => 'Maximum total amount must be at least 0.',
        ];
    }
}
