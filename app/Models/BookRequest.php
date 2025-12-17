<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_title',
        'author_name',
        'message',
        'requested_by_user',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by_user');
    }
}
