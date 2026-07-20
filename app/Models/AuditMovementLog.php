<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditMovementLog extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'from_wh', 'to_wh', 'zone', 'qty', 'raw_date'];
}