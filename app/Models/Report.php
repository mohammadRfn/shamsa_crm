<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports'; // تعیین نام جدول در دیتابیس

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
        'end_date',
        'technical_approval',
        'request_approval',
        'supply_approval',
        'ceo_approval',
        'status',
        'image',
        'audio',
        'video',
    ];

    // روابط با سایر مدل‌ها
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
