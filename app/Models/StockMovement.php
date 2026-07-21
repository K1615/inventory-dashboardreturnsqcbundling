<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $primaryKey = 'tx_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    // Replaces the old part() relationship
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}