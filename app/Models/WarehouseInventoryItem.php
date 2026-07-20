<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'warehouse', 'zone', 'qty', 'last_moved'];
    protected $casts = ['last_moved' => 'datetime'];
}