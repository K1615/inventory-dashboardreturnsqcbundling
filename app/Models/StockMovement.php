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