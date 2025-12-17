<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value or return the provided default.
     */
    public static function getValue(string $key, $default = null)
    {
        $value = static::where('key', $key)->value('value');
        return $value !== null ? $value : $default;
    }

    /**
     * Create or update a setting.
     */
    public static function setValue(string $key, $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

