<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category',
        'stock',
        'exp_date'
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'part_id', 'id');
    }
}