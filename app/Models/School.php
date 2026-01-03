<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'principal_name',
        'established_year',
        'total_students',
        'total_teachers',
        'status',
        'description'
    ];

    protected $casts = [
        'established_year' => 'integer',
        'total_students' => 'integer',
        'total_teachers' => 'integer',
        'status' => 'boolean'
    ];
}
