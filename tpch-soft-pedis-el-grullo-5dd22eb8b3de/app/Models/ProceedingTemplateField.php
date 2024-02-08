<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProceedingTemplateField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proceeding_template_id',
        'order_index',
        'label',
        'description',
        'icon',
        'placeholder',
        'input_type',
        'is_required',
        'is_disabled',
        'is_read_only',
        'max_value',
        'min_value',
        'def_value',
        'option_values',
        'ajax_source',
    ];

    protected $casts = [
        'is_required'   => 'boolean',
        'is_disabled'   => 'boolean',
        'is_read_only'  => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(ProceedingTemplate::class);
    }

    public function getOptionValuesAttribute( $option_values)
    {
        return json_decode( $option_values );
    }

    public function setOptionValuesAttribute( $options = [] )
    {
        $this->attributes['option_values'] = json_encode( $options );
    }
}
