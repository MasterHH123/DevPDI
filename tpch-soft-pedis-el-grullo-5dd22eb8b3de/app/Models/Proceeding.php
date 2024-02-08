<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proceeding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'citizen_id',
        'name',
        'description',
        'tags',
        'status',
    ];

    protected $appends = [
        'readable_created_at',
        'status_badge',
        'is_closed',
        'is_active',
    ];

    public const STATUSES = [
        'Abierto',
        'Cerrado',
    ];

    public function citizen()
    {
        return $this->belongsTo(Citizen::class)->withTrashed();
    }

    public function records()
    {
        return $this->hasMany(ProceedingRecord::class)->withTrashed();
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->isoFormat('dddd DD [de] MMMM YYYY');
    }

    public function getStatusBadgeAttribute()
    {
        return [
            'Abierto'   => '🟢',
            'Cerrado'   => '🟡',
        ][$this->status];
    }

    public function getIsClosedAttribute()
    {
        return $this->status == 'Cerrado';
    }

    public function getIsActiveAttribute()
    {
        return $this->deleted_at == null;
    }
}
