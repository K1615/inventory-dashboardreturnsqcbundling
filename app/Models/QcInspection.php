<?php

// app/Models/QcInspection.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QcInspection extends Model {
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'op', 'itemId', 'product', 'source', 'action', 'status'];
}
