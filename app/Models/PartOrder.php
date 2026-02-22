<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_name',
        'order_date',
        'order_number',
        'part_name',
        'specifications',
        'package',
        'quantity',
        'description',
        'supply_approval',
        'reception_approval',
        'request_approval',
        'ceo_approval',
        'status',
        'approved_by_count',
        'rejected_by_count',
        'last_action_at',
        'last_action_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'quantity' => 'integer',
        'supply_approval' => 'boolean',
        'reception_approval' => 'boolean',
        'request_approval' => 'boolean',
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
            'reception' => $this->reception_approval === null,
            'supply' => $this->supply_approval === null,
            'ceo' => $this->ceo_approval === null,
            default => false,
        };
    }
    protected static function booted()
    {
        static::saving(function ($partOrder) {
            // اگر همه true باشن → approved
            if (
                $partOrder->reception_approval === true
                && $partOrder->supply_approval === true
                && $partOrder->ceo_approval === true
            ) {
                $partOrder->status = 'approved';
            }
            // اگر همه false باشن → failed
            elseif (
                $partOrder->reception_approval === false
                && $partOrder->supply_approval === false
                && $partOrder->ceo_approval === false
            ) {
                $partOrder->status = 'failed';
            }
            // اگر هر کدوم null یا تغییر کرده → pending
            else {
                $partOrder->status = 'pending';
            }
        });
    }
    protected function orderDateJalali(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->order_date ? toJalali($this->order_date) : null
        );
    }
}
