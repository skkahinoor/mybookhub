<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'url',
        'page_title',
        'module',
        'ip_address',
        'country',
        'state',
        'city',
        'user_agent',
    ];
}
