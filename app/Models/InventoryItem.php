<?php

// app/Models/InventoryItem.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model {
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'name', 'category', 'stock', 'price', 'warehouse',
        'minLimit', 'maxLimit', 'auto_reorder', 'reorder_qty',
    ];

    protected $casts = [
        'auto_reorder' => 'boolean',
    ];

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'inventory_item_id', 'id');
    }
}
