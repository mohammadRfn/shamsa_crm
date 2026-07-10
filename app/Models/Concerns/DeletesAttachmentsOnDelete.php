<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait DeletesAttachmentsOnDelete
{
    protected static function bootDeletesAttachmentsOnDelete()
    {
        static::deleting(function ($model) {
            foreach ($model->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        });
    }
}