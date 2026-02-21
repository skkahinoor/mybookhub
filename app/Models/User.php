<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Laravel\Sanctum\HasApiTokens; // Adding the HasApiTokens trait of "Laravel Passport" package (different from Sanctum's one)        // https://laravel.com/docs/9.x/passport#:~:text=add%20the,Laravel%5CPassport%5CHasApiTokens

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    // use  HasApiTokens,  HasFactory, Notifiable, \Laravel\Passport\HasApiTokens; // Adding the HasApiTokens trait of "Laravel Passport" package (different from Sanctum's one)        // https://laravel.com/docs/9.x/passport#:~:text=add%20the,Laravel%5CPassport%5CHasApiTokens

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'father_names',
        'institution_id',
        'class',
        'roll_number',
        'gender',
        'dob',
        'added_by',
        'phone',
        'password',
        'status',
        'country_id',
        'state_id',
        'district_id',
        'block_id',
        'address',
        'pincode',
        'profile_image',
        'role_id',
        'confirm',
        'wallet_balance',
        'is_wallet_credited'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTypeAttribute()
    {
        // Check Spatie Roles first 
        if ($this->hasRole('admin') || $this->hasRole('superadmin')) {
            return 'admin';
        }
        if ($this->hasRole('vendor')) {
            return 'vendor';
        }
        if ($this->hasRole('sales')) {
            return 'sales'; // Not in original Admin types, but useful
        }
        // Fallback to legacy behavior if needed (e.g. checks role_id directly)
        if ($this->role_id == 1 || $this->role_id === 'admin' || $this->role_id === 'superadmin') return 'admin';
        if ($this->role_id == 2 || $this->role_id === 'vendor') return 'vendor';
        if ($this->role_id == 3 || $this->role_id === 'sales') return 'sales';

        return 'user'; // Default
    }

    public function institution()
    {
        return $this->belongsTo(InstitutionManagement::class, 'institution_id');
    }
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
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }
    public function assignedRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function salesExecutive()
    {
        return $this->hasOne(SalesExecutive::class, 'user_id');
    }

    public function vendorPersonal()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    public function vendorBusiness()
    {
        // Link to VendorsBusinessDetail through the Vendor model
        // Note: This assumes one user has one vendor profile
        return $this->hasOneThrough(
            VendorsBusinessDetail::class,
            Vendor::class,
            'user_id',    // Foreign key on vendors table
            'vendor_id',  // Foreign key on vendors_business_details table
            'id',         // Local key on users table
            'id'          // Local key on vendors table
        );
    }

    public function vendorBank()
    {
        return $this->hasOneThrough(
            VendorsBankDetail::class,
            Vendor::class,
            'user_id',
            'vendor_id',
            'id',
            'id'
        );
    }

    // Accessor for AdminController compatibility ('mobile' -> 'phone')
    public function getMobileAttribute()
    {
        return $this->phone;
    }

    // Accessor for AdminController compatibility ('image' -> 'profile_image')
    public function getImageAttribute()
    {
        return $this->profile_image;
    }

    public function getVendorIdAttribute()
    {
        return $this->vendorPersonal ? $this->vendorPersonal->id : 0;
    }

    public function wallet_transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
    }
}
