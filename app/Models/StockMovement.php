<?php

// app/Models/StockMovement.php
//
// A handoff record only. This submodule does NOT apply movements to real
// stock quantities — a separate Stock Movements submodule owns that.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id', 'type', 'qty', 'source_type', 'source_id', 'created_by', 'notes',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id', 'id');
    }
}
