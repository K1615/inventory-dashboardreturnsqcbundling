<?php

// app/Models/ReturnsAuditLog.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReturnsAuditLog extends Model {
    protected $fillable = ['op', 'stream', 'info', 'outcome', 'statusType'];
}
