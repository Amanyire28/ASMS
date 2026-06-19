<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a specific setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Get all settings from cache or database
        $settings = self::getAllCached();

        // Return the value if exists, otherwise return default
        if (isset($settings[$key])) {
            $setting = $settings[$key];

            // Decode JSON values
            if ($setting['type'] === 'json') {
                return json_decode($setting['value'], true);
            }

            // Cast integers
            if ($setting['type'] === 'integer') {
                return (int) $setting['value'];
            }

            // Cast booleans
            if ($setting['type'] === 'boolean') {
                return filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN);
            }

            return $setting['value'];
        }

        return $default;
    }

    /**
     * Get configured exam types with normalization and default fallback.
     *
     * @return array
     */
    public static function examTypes(): array
    {
        $types = self::get('exam_types') ?? [];
        if (empty($types)) {
            return [[
                'id' => 'Final',
                'label' => 'Final Exam',
                'max_marks' => 100,
                'weight' => 100,
                'order' => 1,
            ]];
        }

        $normalized = [];
        foreach ($types as $index => $type) {
            $normalized[] = [
                'id' => trim($type['id'] ?? 'Final'),
                'label' => trim($type['label'] ?? 'Final Exam'),
                'max_marks' => (float) ($type['max_marks'] ?? 100),
                'weight' => isset($type['weight']) ? (float) $type['weight'] : 100,
                'order' => isset($type['order']) ? (int) $type['order'] : $index + 1,
            ];
        }

        return $normalized;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @return SchoolSetting
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general', ?string $description = null)
    {
        // Encode arrays/objects to JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = 'json';
        }

        // Convert boolean to string
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        // Update or create the setting
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        // Clear cache
        self::clearCache();

        return $setting;
    }

    /**
     * Get all settings grouped by key
     * Cached for performance
     *
     * @return array
     */
    public static function getAllCached(): array
    {
        return Cache::remember('school_settings', 3600, function () {
            return self::all()->keyBy('key')->map(function ($setting) {
                return [
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                ];
            })->toArray();
        });
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Clear the settings cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget('school_settings');
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        $settings = self::getAllCached();
        return isset($settings[$key]);
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public static function remove(string $key): bool
    {
        $deleted = self::where('key', $key)->delete();

        if ($deleted) {
            self::clearCache();
        }

        return $deleted > 0;
    }
}
