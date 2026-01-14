<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequestReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_request_id',
        'reply_by',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookRequest()
    {
        return $this->belongsTo(BookRequest::class, 'book_request_id');
    }
}

