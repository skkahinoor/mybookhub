<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicProfile extends Model
{
    use HasFactory;

    protected $table = 'academic_profiles';

    protected $fillable = [
        'user_id',
        'education_level_id',
        'board_id',
        'class_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function educationLevel()
    {
        // Maps to sections table
        return $this->belongsTo(Section::class, 'education_level_id');
    }

    public function board()
    {
        // Maps to categories table
        return $this->belongsTo(Category::class, 'board_id');
    }

    public function class()
    {
        return $this->belongsTo(InstitutionClass::class, 'class_id');
    }
}

