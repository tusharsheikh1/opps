<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'phone',
        'message',
        'type',
        'status',
        'response',
        'sent_at'
    ];

    protected $dates = [
        'sent_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the order that owns the SMS log.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user (admin) who sent the SMS.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include sent SMS.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope a query to only include failed SMS.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include pending SMS.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter by SMS type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by phone number.
     */
    public function scopeForPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone;
        
        // Format phone number for display (e.g., +880 1XXX-XXXXXX)
        if (strlen($phone) >= 11) {
            if (substr($phone, 0, 3) === '880') {
                return '+880 ' . substr($phone, 3, 4) . '-' . substr($phone, 7);
            }
        }
        
        return $phone;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'sent':
                return '<span class="badge badge-success">Sent</span>';
            case 'failed':
                return '<span class="badge badge-danger">Failed</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    /**
     * Get type badge HTML
     */
    public function getTypeBadgeAttribute()
    {
        switch ($this->type) {
            case 'order_confirmation':
                return '<span class="badge badge-info">Order Confirmation</span>';
            case 'status_update':
                return '<span class="badge badge-primary">Status Update</span>';
            case 'custom':
                return '<span class="badge badge-purple">Custom</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }
}