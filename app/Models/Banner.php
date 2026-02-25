<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'type',
        'link',
        'title',
        'alt',
        'status',
    ];

    public function getImageAttribute($value)
{
    if ($value) {
        return asset('front/images/banner_images/' . $value);
    }

    return null;
}
}
