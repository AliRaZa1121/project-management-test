<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'total_amount',
        'status',
        'approved_at'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
