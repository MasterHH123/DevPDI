<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AppLogger;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // If already logged in
        if( Auth::guard('web')->check() )
            return redirect()->route('home');
        
        // otherwise, show login form
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        if( $request->ajax() ){

            // Login via ajax
            if (Auth::guard('web')->attempt([
                'email'             => $request->email,
                'password'          => $request->password,
                'status'            => 1, // mus be active
            ])) {

                AppLogger::log("Inicio de sesión", $request->all());
                
                return response()->json([
                    'success'   => true,
                    'message'   => 'Bienvenido de nuevo!',
                    'csrf'      => csrf_token(),
                ]);
            }

            // Failed
            abort(401, 'Usuario o clave incorrectos');
        }

        $user = User::where(function($q)use($request){
            $q
            ->where('email', $request->user )
            ->orWhere('username', $request->user );
        })
        ->active()
        ->first();

        $auth_passed = $user ? password_verify($request->password, $user->password) : false;

        if ( $auth_passed ) {
            // Authentication passed...
            Auth::login($user);
            
            AppLogger::log("Inicio de sesión", $request->all());
            
            return redirect()
                ->route('home')
                ->with('welcome_message', __('auth.welcome', [
                        'name' => Auth::guard('web')->user()->first_name,
                ]));
        }

        return redirect()
            ->route('login')
            ->withInput()
            ->with('error', __('auth.failed'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if( $request->ajax() )
            return response()->json([
                'success'   => true,
                'message'   => 'Bye!'
            ]);
        else
            return redirect()->to('login');
    }
}
