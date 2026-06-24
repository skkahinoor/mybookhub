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
}
