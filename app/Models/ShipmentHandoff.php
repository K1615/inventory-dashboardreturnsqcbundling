<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// A handoff record only. This submodule does NOT apply movements to real 
// stock quantities — a separate Stock Movements submodule owns that.
class ShipmentHandoff extends Model
{
    protected $table = 'shipment_handoffs';

    protected $fillable = [
        'inventory_item_id', 'type', 'qty', 'source_type', 'source_id', 'created_by', 'notes',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id', 'id');
    }
}