<?php

// app/Models/SystemLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model 
{
    public $timestamps = false;
    
    protected $fillable = ['user', 'action', 'created_at'];

    // Add this to cast the string back into a Carbon datetime object
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
