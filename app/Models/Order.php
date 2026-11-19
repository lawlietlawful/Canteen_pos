<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'canteen_pos.orders';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $fillable = [
        'idno',
        'customer_name',
        'order_date',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date'   => 'datetime',
    ];

    /**
     * Order belongs to a User (Cashier) via idno
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idno', 'idno');
    }

    /**
     * Order has many OrderItems
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Order has one Payment
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }
}

