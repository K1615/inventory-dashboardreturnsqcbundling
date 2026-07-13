<?php

// app/Models/BundleRequest.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BundleRequest extends Model {
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'requester', 'type', 'details', 'recipe', 'status', 'approver'];
    protected $casts = ['recipe' => 'array'];
}
