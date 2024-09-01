<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProceedingRecordAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceeding_record_id',
        'name',
        'file_ext',
        'path',
    ];

    protected $appends = [
        'full_path',
    ];

    public const ALLOWED_EXT = [
        'xlsx',
        'xls',
        'doc',
        'docx',
        'ppt',
        'pptx',
        'pdf',
        'png',
        'jpg',
        'jpeg',
        'webp',
    ];

    public function record()
    {
        return $this->belongsTo(ProceedingRecord::class);
    }

    public function getFullPathAttribute()
    {
        return asset( 'storage/' . $this->path );
    }
}
