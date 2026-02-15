<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'part_name',
        'request_date',
        'request_number',
        'serial_number',
        'device_model',
        'issue_description',
        'activity_report',
        'used_parts_list',
        'workers_count',
        'hours_per_worker',
        'total_man_hours',
        'end_date',
        'technical_approval',
        'request_approval',
        'supply_approval',
        'ceo_approval',
        'status',
        'approved_by_count',
        'rejected_by_count',
        'last_action_at',
        'last_action_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'end_date' => 'date',
        'workers_count' => 'integer',
        'hours_per_worker' => 'decimal:2',
        'total_man_hours' => 'decimal:2',
        'technical_approval' => 'boolean',
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
            'reception', 'supply' => $query->whereIn('status', ['new', 'pending']),
            'ceo' => $query,
            default => $query->where('id', 0), // No access
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

    public function isFullyApproved(): bool
    {
        return $this->request_approval === 1
            && $this->supply_approval === 1
            && $this->ceo_approval === 1;
    }

    public function isFullyRejected(): bool
    {
        return $this->request_approval === 0
            && $this->supply_approval === 0
            && $this->ceo_approval === 0;
    }

    public function hasAnyRejection(): bool
    {
        return $this->request_approval === 0
            || $this->supply_approval === 0
            || $this->ceo_approval === 0;
    }

    public function getPendingApprovers(): array
    {
        $pending = [];

        if ($this->request_approval === null) {
            $pending[] = 'reception';
        }
        if ($this->supply_approval === null) {
            $pending[] = 'supply';
        }
        if ($this->ceo_approval === null) {
            $pending[] = 'ceo';
        }

        return $pending;
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

    public function hasApprovedBy(User $user): bool
    {
        return match ($user->role) {
            'reception' => $this->request_approval === 1,
            'supply' => $this->supply_approval === 1,
            'ceo' => $this->ceo_approval === 1,
            default => false,
        };
    }

    public function hasRejectedBy(User $user): bool
    {
        return match ($user->role) {
            'reception' => $this->request_approval === 0,
            'supply' => $this->supply_approval === 0,
            'ceo' => $this->ceo_approval === 0,
            default => false,
        };
    }

    protected static function booted()
    {
        static::saving(function ($report) {
            if ($report->workers_count && $report->hours_per_worker) {
                $report->total_man_hours = $report->workers_count * $report->hours_per_worker;
            }
        });
    }
}
