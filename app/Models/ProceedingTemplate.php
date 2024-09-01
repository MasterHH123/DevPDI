<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProceedingTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'tag_version',
        'work_shift_id',
    ];

    protected $appends = [
        'readable_created_at',
        'is_active',
    ];

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function fields()
    {
        return $this->hasMany(ProceedingTemplateField::class);
    }

    public function versionLogs()
    {
        return $this->hasMany(ProceedingTemplateVersionLog::class);
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsActiveAttribute()
    {
        return $this->deleted_at == null;
    }
}
