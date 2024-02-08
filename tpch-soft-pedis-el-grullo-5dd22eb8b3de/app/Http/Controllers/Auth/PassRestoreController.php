<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserPasswordReset as PassReset;
use App\Helpers\AppLogger;
use Str;
use Mail;

class PassRestoreController extends Controller
{
    public function forgotPass(Request $request)
    {   
        // If already logged in
        if( Auth::guard('web')->check() )
            return redirect()->route('home');

        return view('auth.forgot');
    }

    public function restorePass(Request $request)
    {   
        $email = $request->email;
        $user  = User::where('email', $email)->first();

        if( !$user )
            return redirect()
            ->route('forgot_restore')
            ->withInput()
            ->with('error', "No fué posible recuperar tu clave");

        // Set user new temp pass
        $new_pass = Str::random(12);
        $user->update([
            'password' => $new_pass
        ]);

        // Send reset email
        $user->notify(new PassReset($new_pass));

        //
        AppLogger::log("Recuperación de clave solicitada", $request->all());

        return redirect()
            ->route('forgot_restore')
            ->withInput()
            ->with('message', "Enviamos una clave temporal a tu correo");
    }
}
