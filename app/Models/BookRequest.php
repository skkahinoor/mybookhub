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
        'publisher_name',
        'message',
        'admin_reply',
        'requested_by_user',
        'vendor_id',
        'district_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by_user');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function replies()
    {
        return $this->hasMany(BookRequestReply::class, 'book_request_id')->orderBy('created_at', 'asc');
    }
}
