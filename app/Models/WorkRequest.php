<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WorkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'request_date',
        'serial_number',
        'device_model',
        'equipment_name',
        'request_unit',
        'contact_person',
        'contact_phone',
        'work_description',
        'workflow_description',
        'issue_description',
        'request_type',
        'estimated_cost',
        'initial_price_result',
        'final_cost',
        'responsible_officer',
        'request_approval',
        'supply_approval',
        'ceo_approval',
        'status',
        'payment_status',
        'invoice_number',
        'accounting_document',
        'receipt_document',
        'bank_name',
        'approved_by_count',
        'rejected_by_count',
        'last_action_at',
        'last_action_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'request_approval' => 'boolean',
        'supply_approval' => 'boolean',
        'ceo_approval' => 'boolean',
        'approved_by_count' => 'integer',
        'rejected_by_count' => 'integer',
        'last_action_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'reportable');
    }


    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function lastActionBy()
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }

    // Scopes
    public function scopeForRole($query, string $role)
    {
        return match ($role) {
            'technician' => $query->where('user_id', auth()->id()),

            'reception' => $query->where(function ($q) {
                $q->whereIn('status', ['new', 'pending'])
                    ->orWhereNotNull('reception_approval');
            }),

            'supply' => $query->where(function ($q) {
                $q->whereIn('status', ['new', 'pending'])
                    ->orWhereNotNull('supply_approval');
            }),

            'ceo' => $query,
            default => $query->where('id', 0),
        };
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['new', 'pending']);
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper Methods
    // درست - با boolean cast سازگار
    public function isFullyApproved(): bool
    {
        return $this->request_approval === true
            && $this->supply_approval === true
            && $this->ceo_approval === true;
    }

    public function isFullyRejected(): bool
    {
        return $this->request_approval === false
            && $this->supply_approval === false
            && $this->ceo_approval === false;
    }

    public function hasAnyRejection(): bool
    {
        return $this->request_approval === false
            || $this->supply_approval === false
            || $this->ceo_approval === false;
    }

    public function canBeApprovedBy(User $user): bool
    {
        return match ($user->role) {
            'reception' => $this->request_approval === null,
            'supply' => $this->supply_approval === null,
            'ceo' => $this->ceo_approval === null,
            default => false,
        };
    }
    protected static function booted()
    {
        static::saving(function ($workRequest) {
            // اگر همه true باشن → approved
            if (
                $workRequest->request_approval === true
                && $workRequest->supply_approval === true
                && $workRequest->ceo_approval === true
            ) {
                $workRequest->status = 'approved';
            }
            // اگر همه false باشن → rejected  
            elseif (
                $workRequest->request_approval === false
                && $workRequest->supply_approval === false
                && $workRequest->ceo_approval === false
            ) {
                $workRequest->status = 'rejected';
            }
            // اگر هر کدوم null یا تغییر کرده → pending
            else {
                $workRequest->status = 'pending';
            }
        });
    }

    public function isPaid(): bool
    {
        return !empty($this->payment_status);
    }

    protected function requestDateJalali(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->request_date ? toJalali($this->request_date) : '---'
        );
    }
}
