<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function partOrders()
    {
        return $this->hasMany(PartOrder::class);
    }

    public function workRequests()
    {
        return $this->hasMany(WorkRequest::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    // Role Helper Methods
    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function isSupply(): bool
    {
        return $this->role === 'supply';
    }

    public function isCEO(): bool
    {
        return $this->role === 'ceo';
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['reception', 'supply', 'ceo']);
    }

    public function canApprove(): bool
    {
        return $this->isApprover();
    }

    public function canCreateReport(): bool
    {
        return $this->isTechnician();
    }

    public function canViewAllReports(): bool
    {
        return in_array($this->role, ['reception', 'supply', 'ceo']);
    }
}
