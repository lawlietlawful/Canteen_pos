<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProduct extends Model
{
    use HasFactory;

    protected $table = 'inventory.products';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'description',
        'category_id',
        'supplier_id',
        'unit',
        'cost_price',
        'selling_price',
        'quantity_on_hand',
        'reorder_level',
        'status',
    ];

    protected $casts = [
        'cost_price'       => 'decimal:2',
        'selling_price'    => 'decimal:2',
        'quantity_on_hand' => 'integer',
        'reorder_level'    => 'integer',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }
}
