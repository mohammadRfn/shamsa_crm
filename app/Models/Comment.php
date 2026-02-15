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

    public function scopeForUser($query, User $user)
    {
        if ($user->isTechnician()) {
            return $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('is_visible_to_technician', true);
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
