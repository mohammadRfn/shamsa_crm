<?php

namespace App\Models;

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
        'ceo_approval',
        'status',
        'image',
        'audio',
        'video',
    ];

    // رابطه با مدل User (هر سفارش به یک کاربر تعلق دارد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}