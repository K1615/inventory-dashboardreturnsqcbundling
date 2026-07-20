<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementRequest extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_inventory_item_id', 'requester', 'name', 'from_wh', 'from_zone', 'to_wh', 'to_zone', 'qty', 'planned_date'];
}