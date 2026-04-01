<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderQueryMessage extends Model
{
    protected $fillable = [
        'order_query_id',
        'user_id',
        'message',
        'attachment',
        'sender_type'
    ];

    public function orderQuery()
    {
        return $this->belongsTo(OrderQuery::class, 'order_query_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
