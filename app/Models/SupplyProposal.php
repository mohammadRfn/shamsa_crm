<?php

namespace App\Models;

use App\Models\Concerns\DeletesAttachmentsOnDelete;
use Illuminate\Database\Eloquent\Model;

class SupplyProposal extends Model
{
    use DeletesAttachmentsOnDelete;
    protected $fillable = [
        'part_order_id',
        'part_name',
        'supplier_name',
        'unit_price',
        'quantity',
        'estimated_delivery',
        'note',
        'status',
        'ceo_note',
        'selected_at',
        'created_by',
    ];

    protected $casts = [
        'unit_price'          => 'decimal:2',
        'estimated_delivery'  => 'date',
        'selected_at'         => 'datetime',
        'created_by'          => 'integer',
        'part_order_id'       => 'integer',
    ];

    // ── وضعیت‌ها ────────────────────────────────────────────────────────
    const STATUSES = [
        'pending'   => 'در انتظار بررسی',
        'approved'  => 'تایید مدیریت',
        'rejected'  => 'رد شده',
        'ordered'   => 'سفارش داده شد',
        'delivered' => 'تحویل شد',
    ];

    // فقط supply این وضعیت‌ها رو میتونه ست کنه
    const SUPPLY_STATUSES = ['ordered', 'delivered'];

    // فقط CEO این وضعیت‌ها رو میتونه ست کنه
    const CEO_STATUSES = ['approved', 'rejected'];

    // ── روابط ────────────────────────────────────────────────────────────
    public function partOrder()
    {
        return $this->belongsTo(PartOrder::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'reportable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')->latest();
    }

    // ── Helpers ──────────────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getEstimatedDeliveryJalaliAttribute(): ?string
    {
        if (!$this->estimated_delivery) return null;
        return toJalali($this->estimated_delivery->format('Y-m-d'));
    }

    public function canBeChangedBySupply(): bool
    {
        return true; // supply همیشه میتونه وضعیت رو عوض کنه
    }

    public function canBeChangedByCeo(): bool
    {
        return true; // CEO هم همیشه میتونه وضعیت رو عوض کنه
    }
}
