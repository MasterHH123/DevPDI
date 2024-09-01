<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProceedingTemplateVersionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceeding_template_id',
        'name',
        'description',
        'tag_version',
        'fields',
    ];

    public function template()
    {
        return $this->belongsTo(ProceedingTemplate::class);
    }

    public function getFieldsAttribute( $fields)
    {
        return json_decode( $fields );
    }

    public function setFieldsAttribute( $fields = [] )
    {
        $this->attributes['fields'] = json_encode( $fields );
    }
}
