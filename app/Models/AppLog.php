<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'payload',
        'related_user_id',
    ];

    protected $appends = [
        'readable_created_at',
    ];

    public function relatedUser()
    {
        return $this->belongsTo(User::class);
    }

    public function getPayloadAttribute( $payload )
    {
        return json_decode( $payload );
    }

    public function setPayloadAttribute( $payload = [] )
    {
        $this->attributes['payload'] = json_encode( $payload );
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->isoFormat('dddd DD [de] MMMM YYYY');
    }
}
