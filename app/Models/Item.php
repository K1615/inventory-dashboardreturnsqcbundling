<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    // Specify string primary key behavior
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    // Relationships mapped to other submodules
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function stockMovementRequests()
    {
        return $this->hasMany(StockMovementRequest::class, 'item_id');
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'item_id');
    }

    public function shipmentHandoffs()
    {
        return $this->hasMany(ShipmentHandoff::class, 'item_id');
    }
    
    // Add additional relationships for QC and RMA as needed
}