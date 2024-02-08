<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citizen extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'second_last_name',
        'avatar',
        'gender',
        'phone',
        'address_street',
        'address_zipcode',
        'address_lat',
        'address_lng',
        'birth_date',
        'age',
        'curp',
        'rfc',
    ];

    protected $appends = [
        'avatar_full_path',
        'gender_display',
        'birth_date_std',
        'is_active',
    ];

    protected $dates = [
        'birth_date'
    ];

    public function scopeFemale($q)
    {
        return $q->where('gender', 'F');
    }

    public function scopeMale($q)
    {
        return $q->where('gender', 'M');
    }

    public function scopeNoGenre($q)
    {
        return $q->where('gender', 'O');
    }

    public function getGenderDisplayAttribute()
    {
        $genders = [
            'M' => 'Masculino',
            'F' => 'Femenino',
            'O' => 'No especificado',
        ];

        return $this->gender ? $genders[ $this->gender ] : null;
    }

    public function getAvatarFullPathAttribute()
    {
        $icons = [
            'M' => asset('/img/icons/male.svg'),
            'F' => asset('/img/icons/female.svg'),
            'O' => asset('/img/icons/rainbow.svg'),
        ];

        return $this->avatar ? 
            asset('storage/' . $this->avatar) :
            $icons[ $this->gender ];
    }

    public function setBirthDateAttribute($birth_date)
    {
        $this->attributes['birth_date'] = $birth_date;
        $this->attributes['age'] = \Carbon\Carbon::parse($birth_date)->diffInYears();
    }

    public function getBirthDateStdAttribute()
    {
        return substr($this->birth_date, 0, 10);
    }

    public function getIsActiveAttribute()
    {
        return $this->deleted_at == null;
    }

    public function proceedingRecords()
    {
        return $this->hasMany(ProceedingRecord::class);
    }
}
