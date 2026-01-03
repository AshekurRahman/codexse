<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset(static::$cache[$key])) {
            $cached = static::$cache[$key];
            return ($cached === null || $cached === '') ? $default : $cached;
        }

        $value = Cache::remember("seo_setting_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        // Treat empty strings as null so defaults are used
        if ($value === null || $value === '') {
            static::$cache[$key] = $default;
        } else {
            static::$cache[$key] = $value;
        }

        return static::$cache[$key];
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("seo_setting_{$key}");
        static::$cache[$key] = $value;
    }

    public static function getByGroup(string $group): array
    {
        return Cache::remember("seo_settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public static function clearCache(): void
    {
        static::$cache = [];
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("seo_setting_{$setting->key}");
        }
        Cache::forget('seo_settings_group_general');
        Cache::forget('seo_settings_group_social');
        Cache::forget('seo_settings_group_schema');
        Cache::forget('seo_settings_group_sitemap');
        Cache::forget('seo_settings_group_robots');
        Cache::forget('seo_settings_group_verification');
    }

    public static function getAll(): array
    {
        return Cache::remember('seo_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
