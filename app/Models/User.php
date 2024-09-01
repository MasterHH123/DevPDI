<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        // Auth
        'username',
        'email',
        'email_verified_at',
        'password',
        'status',
        'is_admin',

        // Identification
        'first_name',
        'last_name',
        'second_last_name',
        'avatar',
        'gender',
    ];

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
        'status'            => 'boolean',
        'is_admin'          => 'boolean',
    ];

    protected $appends = [
        'avatar_full_path',
        'gender_display',
        'is_active',
    ];

    public function setPasswordAttribute($plain_password)
    {
        $this->attributes['password'] = bcrypt( $plain_password );
    }

    public function getAvatarFullPathAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('/img/icons/user.svg');
    }
    
    public function getGenderDisplayAttribute()
    {
        $genders = [
            'M' => 'Masculino',
            'F' => 'Femenino',
            'O' => 'Otro',
        ];

        return $this->gender ? $genders[ $this->gender ] : null;
    }

    public function getIsActiveAttribute()
    {
        return $this->deleted_at == null;
    }

    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }

    public function proceedingRecords()
    {
        return $this->hasMany(ProceedingRecord::class);
    }

    public function workShifts()
    {
        return $this->belongsToMany(WorkShift::class, 'user_work_shifts');
    }
}
