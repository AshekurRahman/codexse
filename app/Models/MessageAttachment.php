<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'is_delivery',
    ];

    protected $casts = [
        'is_delivery' => 'boolean',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getIconAttribute(): string
    {
        return match ($this->file_type) {
            'image' => 'heroicon-o-photo',
            'video' => 'heroicon-o-video-camera',
            'audio' => 'heroicon-o-musical-note',
            'document' => 'heroicon-o-document-text',
            'spreadsheet' => 'heroicon-o-table-cells',
            'presentation' => 'heroicon-o-presentation-chart-bar',
            'archive' => 'heroicon-o-archive-box',
            'pdf' => 'heroicon-o-document',
            default => 'heroicon-o-paper-clip',
        };
    }

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    public function isDocument(): bool
    {
        return in_array($this->file_type, ['document', 'pdf', 'spreadsheet', 'presentation']);
    }

    public function isPreviewable(): bool
    {
        return $this->isImage() || $this->file_type === 'pdf';
    }

    public static function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'text/rtf',
        ])) {
            return 'document';
        }

        if (in_array($mimeType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ])) {
            return 'spreadsheet';
        }

        if (in_array($mimeType, [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ])) {
            return 'presentation';
        }

        if (in_array($mimeType, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/gzip',
        ])) {
            return 'archive';
        }

        return 'other';
    }
}
