<?php

namespace App\Helpers;
use App\Models\AppLog;

class AppLogger
{
    public static function log($description, $payload = [])
    {
        $user = request()->user();

        AppLog::create([
            'description'       => $description,
            'payload'           => is_array($payload) ? $payload : (method_exists($payload, 'toArray') ? $payload->toArray() : []),
            'related_user_id'   => $user ? $user->id : null,
        ]);
    }
}
