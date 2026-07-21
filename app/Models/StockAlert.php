<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Points the alert to the master item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}