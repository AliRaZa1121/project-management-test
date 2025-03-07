<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'required_if:status,rejected'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status value.',
            'comment.required_if' => 'Comment is required when rejecting an order.',
        ];
    }
}
