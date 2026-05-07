<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgentContactQueryMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'sender_type',
        'message',
    ];

    public function contactQuery()
    {
        return $this->belongsTo(DeliveryAgentContactQuery::class, 'query_id');
    }
}
