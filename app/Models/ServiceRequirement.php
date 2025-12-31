<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'question',
        'type',
        'options',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public const TYPES = [
        'text' => 'Short Text',
        'textarea' => 'Long Text',
        'select' => 'Single Choice',
        'multiple_select' => 'Multiple Choice',
        'file' => 'File Upload',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }
}
