<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicModal extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'image',
        'link',
        'status',
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('front/images/dynamic_modal_images/' . $value);
        }

        return null;
    }
}

