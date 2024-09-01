<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProceedingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proceeding_id',
        'proceeding_template_id',
        'proceeding_template_tag_version',
        'citizen_id',
        'user_id',
        'notes',
    ];

    protected $appends = [
        'readable_created_at',
        'is_active',
    ];

    public function proceeding()
    {
        return $this->belongsTo(Proceeding::class)->withTrashed();
    }

    public function proceedingTemplate()
    {
        return $this->belongsTo(ProceedingTemplate::class)->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany(ProceedingRecordAttachment::class);
    }

    public function getOriginalTemplate()
    {
        $current_template = $this->proceedingTemplate;

        if( $this->proceeding_template_tag_version == $current_template->tag_version )
            return $current_template->load('fields');
        else
            return $current_template
            ->versionLogs()
            ->where('tag_version', $this->proceeding_template_tag_version)
            ->latest()
            ->first();
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function fieldEntries()
    {
        return $this->hasMany(ProceedingRecordFieldEntry::class);
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->isoFormat('dddd DD [de] MMMM YYYY');
    }

    public function getIsActiveAttribute()
    {
        return $this->deleted_at == null;
    }
}
