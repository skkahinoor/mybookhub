<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgentContactQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(DeliveryAgentContactQueryMessage::class, 'query_id')->orderBy('created_at', 'asc');
    }
}
