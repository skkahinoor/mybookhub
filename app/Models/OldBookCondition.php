<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldBookCondition extends Model
{
    use HasFactory;

    protected $table = 'old_book_conditions';

    protected $fillable = [
        'name',
        'percentage',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];
}
