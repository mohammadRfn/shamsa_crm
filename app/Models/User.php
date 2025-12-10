<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function partOrders()
    {
        return $this->hasMany(PartOrder::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isTechnician()
    {
        return $this->role === 'technician';
    }

    public function isReception()
    {
        return $this->role === 'reception';
    }

    public function isSupply()
    {
        return $this->role === 'supply';
    }

    public function isCeO()
    {
        return $this->role === 'ceo';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['ceo']);
    }
}
