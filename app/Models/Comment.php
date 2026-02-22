<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type',
        'comment',
        'role',
        'status',
        'parent_id',
        'is_visible_to_technician',
    ];

    protected $casts = [
        'is_visible_to_technician' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportable()
    {
        return $this->morphTo();
    }
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->latest();
    }
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
    public function scopeVisibleToTechnician($query)
    {
        return $query->where('is_visible_to_technician', true);
    }

   // در قسمت Scopes، این متد رو اضافه کن:

    /**
     * Scope برای نمایش کامنت‌ها بر اساس نقش
     */
    public function scopeForUser($query, $user)
    {
        // CEO همه چیز رو می‌بینه
        if ($user->isCEO()) {
            return $query;
        }

        // تعمیرکار: همه کامنت‌های آیتم‌هایی که متعلق به خودشه
        // (این scope بعد از اینکه query روی یه reportable خاص اجرا میشه،
        //  پس reportable_id و reportable_type قبلاً فیلتر شدن)
        if ($user->isTechnician()) {
            // چون تعمیرکار فقط آیتم‌های خودشو می‌بینه (از controller چک شده)
            // پس همه کامنت‌های اون آیتم رو باید ببینه
            return $query;
        }

        // پذیرش و تامین:
        // ۱. کامنت‌هایی که خودشون نوشتن (parent یا reply)
        // ۲. کامنت‌هایی که کسی به اونها ریپلای زده
        //    (یعنی id این کامنت، parent_id یه کامنت دیگه‌ست)
        if ($user->isApprover()) {
            $userId = $user->id;

            return $query->where(function ($q) use ($userId) {
                $q
                    // کامنت‌های خودش
                    ->where('user_id', $userId)
                    // یا کامنت‌هایی که کسی بهشون ریپلای داده
                    // (یعنی id این کامنت در parent_id جدول comments وجود داره)
                    ->orWhereIn('id', function ($sub) use ($userId) {
                        $sub->select('parent_id')
                            ->from('comments')
                            ->whereNotNull('parent_id')
                            ->whereIn('parent_id', function ($sub2) use ($userId) {
                                $sub2->select('id')
                                    ->from('comments')
                                    ->where('user_id', $userId);
                            });
                    });
            });
        }

        return $query;
    }
    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    public function softDelete(): void
    {
        $this->update(['status' => 'deleted']);
    }

    public function canBeRepliedBy(User $user): bool
    {
        if ($user->isTechnician()) {
            return $this->reportable->user_id === $user->id;
        }
        return $user->isApprover();
    }
}
