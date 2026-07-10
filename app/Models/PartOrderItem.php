<?php

namespace App\Models;

use App\Models\Concerns\DeletesAttachmentsOnDelete;
use Illuminate\Database\Eloquent\Model;

class PartOrderItem extends Model
{
    use DeletesAttachmentsOnDelete;
    protected $fillable = [
        'part_order_id', 'part_name', 'specifications',
        'package', 'quantity', 'description',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function partOrder()
    {
        return $this->belongsTo(PartOrder::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')->latest();
    }
}