<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $primaryKey = 'tx_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tx_id',
        'date',
        'part_id',
        'type',
        'qty',
        'note',
        'status',
        'user'
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class, 'part_id', 'id');
    }
}
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
