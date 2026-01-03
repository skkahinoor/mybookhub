<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the state that owns the district.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the blocks for the district.
     */
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
