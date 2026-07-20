<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRequest extends Model
{
    protected $table = 'requests';
    
    protected $fillable = ['type', 'requestor', 'reviewer', 'outcome', 'target_item_id', 'proposed_data'];

    protected $casts = [
        'proposed_data' => 'array'
    ];
}