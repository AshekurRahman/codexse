<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'schema_markup',
        'noindex',
        'nofollow',
    ];

    protected function casts(): array
    {
        return [
            'schema_markup' => 'array',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRobotsDirective(): string
    {
        $directives = [];

        if ($this->noindex) {
            $directives[] = 'noindex';
        } else {
            $directives[] = 'index';
        }

        if ($this->nofollow) {
            $directives[] = 'nofollow';
        } else {
            $directives[] = 'follow';
        }

        return implode(', ', $directives);
    }

    public function getTitle(): string
    {
        return $this->meta_title ?? $this->og_title ?? '';
    }

    public function getDescription(): string
    {
        return $this->meta_description ?? $this->og_description ?? '';
    }
}
