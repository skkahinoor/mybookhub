<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_name',
        'subcategory_icon',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'subcategory_id', 'subject_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }
}
