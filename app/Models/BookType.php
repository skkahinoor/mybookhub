<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookType extends Model
{
    use HasFactory;

    protected $table = 'book_types';

    protected $fillable = [
        'book_type',
        'book_type_icon',
        'status',
    ];

 public function getImageAttribute($value)
    {
        if ($value) {
            return asset('admin/images/bookType/' . $value);
        }

        return null;
    }
}
