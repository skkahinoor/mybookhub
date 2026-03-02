<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_icon',
        'status',
    ];

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'class_subjects', 'subject_id', 'subcategory_id');
    }
}
