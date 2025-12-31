<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'category_id',
        'title',
        'slug',
        'description',
        'requirements',
        'budget_type',
        'budget_min',
        'budget_max',
        'deadline',
        'duration_type',
        'skills_required',
        'experience_level',
        'attachments',
        'status',
        'visibility',
        'is_featured',
        'proposals_count',
        'views_count',
        'published_at',
        'closes_at',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'skills_required' => 'array',
        'attachments' => 'array',
        'is_featured' => 'boolean',
        'deadline' => 'date',
        'published_at' => 'datetime',
        'closes_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $baseSlug = Str::slug($job->title);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $job->slug = $slug;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(JobProposal::class);
    }

    public function acceptedProposal(): HasOne
    {
        return $this->hasOne(JobProposal::class)->where('status', 'accepted');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(JobContract::class);
    }

    public function conversation(): MorphOne
    {
        return $this->morphOne(Conversation::class, 'conversationable');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('closes_at')
              ->orWhere('closes_at', '>', now());
        });
    }

    // Accessors
    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_type === 'hourly') {
            if ($this->budget_min && $this->budget_max) {
                return '$' . number_format($this->budget_min) . ' - $' . number_format($this->budget_max) . '/hr';
            } elseif ($this->budget_min) {
                return 'From $' . number_format($this->budget_min) . '/hr';
            } elseif ($this->budget_max) {
                return 'Up to $' . number_format($this->budget_max) . '/hr';
            }
            return 'Hourly';
        }

        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min) . ' - $' . number_format($this->budget_max);
        } elseif ($this->budget_min) {
            return 'From $' . number_format($this->budget_min);
        } elseif ($this->budget_max) {
            return 'Up to $' . number_format($this->budget_max);
        }
        return 'Fixed Price';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getDurationTextAttribute(): ?string
    {
        if (!$this->duration_type) {
            return null;
        }

        return match ($this->duration_type) {
            'one_time' => 'One-time project',
            'ongoing' => 'Ongoing project',
            default => ucfirst(str_replace('_', ' ', $this->duration_type)),
        };
    }

    // Status checks
    public function isOpen(): bool
    {
        return $this->status === 'open' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->closes_at && $this->closes_at->isPast();
    }

    public function canAcceptProposals(): bool
    {
        return $this->isOpen();
    }

    public function hasAcceptedProposal(): bool
    {
        return $this->status === 'in_progress' && $this->acceptedProposal()->exists();
    }

    public const STATUSES = [
        'draft' => 'Draft',
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const BUDGET_TYPES = [
        'fixed' => 'Fixed Price',
        'hourly' => 'Hourly Rate',
    ];

    public const DURATION_TYPES = [
        'one_time' => 'One-time Project',
        'ongoing' => 'Ongoing Project',
    ];

    public const EXPERIENCE_LEVELS = [
        'entry' => 'Entry Level',
        'intermediate' => 'Intermediate',
        'expert' => 'Expert',
    ];

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    public static function getBudgetTypes(): array
    {
        return self::BUDGET_TYPES;
    }

    public static function getDurationTypes(): array
    {
        return self::DURATION_TYPES;
    }

    public static function getExperienceLevels(): array
    {
        return self::EXPERIENCE_LEVELS;
    }
}
