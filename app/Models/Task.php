<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'work_request_id',
        'assigned_to',
        'created_by',
        'status',
        'note',
        'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function workRequest(): BelongsTo
    {
        return $this->belongsTo(WorkRequest::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // گزارشاتی که تعمیرکار زیر این تسک ثبت کرده
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    // سفارشات قطعه‌ای که تعمیرکار زیر این تسک ثبت کرده
    public function partOrders(): HasMany
    {
        return $this->hasMany(PartOrder::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function markAsSeen(): void
    {
        if (is_null($this->seen_at)) {
            $this->update(['seen_at' => now()]);
        }
    }

    public function isNew(): bool
    {
        return is_null($this->seen_at);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'در انتظار',
            'in_progress' => 'در حال انجام',
            'done'        => 'انجام شده',
            default       => '---',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'     => 'badge-warning',
            'in_progress' => 'badge-info',
            'done'        => 'badge-success',
            default       => 'badge-info',
        };
    }
}
