<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionManagement extends Model
{
    use HasFactory;

    protected $table = 'institution_managements';

    protected $fillable = [
        'name',
        'type',
        'board',
        'principal_name',
        'class',
        'contact_number',
        'country_id',
        'state_id',
        'district_id',
        // 'city_id',
        'block_id',
        'pincode',
        'status',
        'added_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // public function addedBy()
    // {
    //     return $this->belongsTo(SalesExecutive::class, 'added_by');
    // }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
    // public function city()
    // {
    //     return $this->belongsTo(City::class, 'city_id');
    // }
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * Get the students for the institution.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'institution_id');
    }

    /**
     * Get the classes for the institution.
     */
    public function institutionClasses()
    {
        return $this->hasMany(InstitutionClass::class, 'institution_id');
    }
}
