<?php

namespace App\Traits;

use App\Models\RecordRead;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasReadTracking
{
    // ─── Relation ────────────────────────────────────────────────────────────

    public function reads(): MorphMany
    {
        return $this->morphMany(RecordRead::class, 'readable');
    }

    // ─── Core methods ────────────────────────────────────────────────────────

    /**
     * ثبت یا به‌روزرسانی زمان مشاهده برای کاربر جاری (یا کاربر مشخص)
     */
    public function markAsReadBy(?int $userId = null): void
    {
        $userId ??= Auth::id();
        if (! $userId) return;

        $this->reads()->updateOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }

    /**
     * آیا این رکورد برای کاربر جاری (یا کاربر مشخص) خوانده‌نشده است؟
     * خوانده‌نشده یعنی: هیچ read_at ثبت نشده  یا  updated_at > read_at
     */
    public function isUnreadBy(?int $userId = null): bool
    {
        $userId ??= Auth::id();
        if (! $userId) return false;

        $read = $this->reads->firstWhere('user_id', $userId);

        if (! $read) {
            // کاربر اصلاً نگاه نکرده — فقط اگه خودش سازنده نیست unread حساب کن
            return $this->user_id !== $userId;
        }

        return $this->updated_at > $read->read_at;
    }

    // ─── Scope برای eager-load بهینه در index ────────────────────────────────

    /**
     * استفاده در index: با یه query واحد reads کاربر جاری رو لود کن
     *
     * $reports = Report::with(['user', 'readByCurrentUser'])->paginate();
     */
    public function scopeWithReadStatus($query, ?int $userId = null): void
    {
        $userId ??= Auth::id();

        $query->with(['reads' => function ($q) use ($userId) {
            $q->where('user_id', $userId);
        }]);
    }
}
