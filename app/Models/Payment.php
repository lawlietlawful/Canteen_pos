<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'canteen_pos.payments';

    protected $primaryKey = 'payment_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid'   => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    /**
     * Payment belongs to an Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}

