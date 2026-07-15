<?php

namespace App\Services\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingsManager
{
    /**
     * Get a setting value by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            [$group, $settingKey] = $this->parseKey($key);

            $setting = Setting::where('group', $group)
                ->where('key', $settingKey)
                ->first();

            if (! $setting) {
                return $default;
            }

            return $this->castValue($setting);
        });
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $type = 'string', bool $isPublic = false): void
    {
        [$group, $settingKey] = $this->parseKey($key);

        // Encrypt sensitive data
        if ($type === 'encrypted') {
            $value = Crypt::encryptString((string) $value);
        }

        // Convert arrays/objects to JSON
        if ($type === 'array' || $type === 'json') {
            $value = json_encode($value);
        }

        Setting::updateOrCreate(
            ['group' => $group, 'key' => $settingKey],
            [
                'value' => $value,
                'type' => $type,
                'is_public' => $isPublic,
            ]
        );

        Cache::forget("setting:{$key}");
    }

    /**
     * Get all settings
     */
    public function getAll(bool $publicOnly = true): array
    {
        $cacheKey = $publicOnly ? 'settings:public' : 'settings:all';

        return Cache::remember($cacheKey, 3600, function () use ($publicOnly) {
            $query = Setting::query();

            if ($publicOnly) {
                $query->where('is_public', true);
            }

            return $query->get()
                ->mapWithKeys(function ($setting) {
                    $key = "{$setting->group}.{$setting->key}";

                    return [$key => $this->castValue($setting)];
                })
                ->toArray();
        });
    }

    /**
     * Get all public settings
     */
    public function getPublic(): array
    {
        return $this->getAll(publicOnly: true);
    }

    /**
     * Get all settings for a specific group
     */
    public function getByGroup(string $group): array
    {
        return Setting::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $this->castValue($setting)];
            })
            ->toArray();
    }

    /**
     * Check if a setting exists
     */
    public function has(string $key): bool
    {
        [$group, $settingKey] = $this->parseKey($key);

        return Setting::where('group', $group)
            ->where('key', $settingKey)
            ->exists();
    }

    /**
     * Delete a setting
     */
    public function forget(string $key): void
    {
        [$group, $settingKey] = $this->parseKey($key);

        Setting::where('group', $group)
            ->where('key', $settingKey)
            ->delete();

        Cache::forget("setting:{$key}");
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        Cache::forget('settings:public');
        Cache::forget('settings:all');
        Setting::all()->each(function ($setting) {
            Cache::forget("setting:{$setting->group}.{$setting->key}");
        });
    }

    /**
     * Parse setting key into group and key
     */
    private function parseKey(string $key): array
    {
        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid setting key format: {$key}. Expected format: 'group.key'");
        }

        return $parts;
    }

    /**
     * Cast setting value to appropriate type
     */
    private function castValue(Setting $setting): mixed
    {
        if ($setting->type === 'encrypted') {
            return Crypt::decryptString($setting->value);
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'array', 'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
}
