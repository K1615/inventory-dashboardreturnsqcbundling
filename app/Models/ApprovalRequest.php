<?php

// app/Models/ApprovalRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $primaryKey = 'reqId';

    protected $fillable = [
        'timestamp',
        'requester',
        'details',
        'supplier',
        'warehouse',
        'status',
        'source', // 'manual' | 'auto'
        'triggered_by_alert_id',
    ];

    // Traceability back to the alert that caused an auto-generated draft.
    public function triggeredByAlert()
    {
        return $this->belongsTo(StockAlert::class, 'triggered_by_alert_id');
    }

    public function items()
    {
        return $this->hasMany(ApprovalRequestItem::class, 'approval_request_id', 'reqId');
    }
}
