<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Replaces the old warehouseInventoryItem() relationship
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}