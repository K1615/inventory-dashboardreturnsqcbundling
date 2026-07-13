<?php

// app/Models/RmaRequest.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RmaRequest extends Model {
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'op', 'itemId', 'product', 'vendor', 'reasons', 'status'];
}
