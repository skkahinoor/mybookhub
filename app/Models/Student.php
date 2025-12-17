<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_names',
        'email',
        'phone',
        'institution_id',
        'class',
        'gender',
        'dob',
        'roll_number',
        'status',
        'added_by'
    ];

    protected $casts = [
        'dob' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the institution that owns the student.
     */
    public function institution()
    {
        return $this->belongsTo(InstitutionManagement::class, 'institution_id');
    }
}
