<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionClass extends Model
{
    use HasFactory;

    protected $table = 'institution_classes';

    protected $fillable = [
        'institution_id',
        'class_name',
        'total_strength',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the institution that owns the class.
     */
    public function institution()
    {
        return $this->belongsTo(InstitutionManagement::class, 'institution_id');
    }
}