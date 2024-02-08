<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProceedingRecordFieldEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proceeding_record_id',
        'proceeding_template_field_id',
        'field_value',
    ];

    public function proceedingRecord()
    {
        return $this->belongsTo(ProceedingRecord::class);
    }

    public function proceedingTemplateField()
    {
        return $this->belongsTo(ProceedingTemplateField::class);
    }
}
