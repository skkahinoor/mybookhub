<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'user_id',
        'ip_address',
        'latitude',
        'longitude',
        'results_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($query) {
            $count = static::count();
            if ($count > 100) {
                // Delete oldest
                $excess = $count - 100;
                $oldestIds = static::orderBy('id', 'asc')->limit($excess)->pluck('id');
                static::whereIn('id', $oldestIds)->delete();
            }
        });
    }
}
