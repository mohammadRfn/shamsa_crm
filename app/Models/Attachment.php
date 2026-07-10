<?php
// app/Models/Attachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Polymorphic relation
    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        return match (true) {
            $this->file_size >= 1_048_576 => round($this->file_size / 1_048_576, 1) . ' MB',
            $this->file_size >= 1_024     => round($this->file_size / 1_024, 1) . ' KB',
            default                       => $this->file_size . ' B',
        };
    }
    public static function resolveFileType(string $mime): string
    {
        return match (true) {
            str_starts_with($mime, 'image/') => 'image',
            $mime === 'application/pdf' => 'pdf',
            in_array($mime, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]) => 'word',
            in_array($mime, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]) => 'excel',
            default => 'file',
        };
    }

    public function isWord(): bool
    {
        return $this->file_type === 'word';
    }

    public function isExcel(): bool
    {
        return $this->file_type === 'excel';
    }
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }
    public function isPdf(): bool
    {
        return $this->file_type === 'pdf';
    }
}
