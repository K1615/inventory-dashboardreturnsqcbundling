<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentHandoff extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Points the received shipment handoff to the master item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}