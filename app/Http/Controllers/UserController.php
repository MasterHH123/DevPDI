<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\AppLogger;
use DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {

            $paginate   = $request->paginate;
            $filters    = json_decode($request->filters);
            $pagination = json_decode($request->pagination);
            $term       = $filters->term;

            $users = User::when($term, function($q)use($term){
                $q->where(function($sq)use($term){
                    $sq
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('second_last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('username', 'like', "%{$term}%")
                    ->orWhereHas('workShifts', function($ssq)use($term){
                        $ssq->where('name', 'like', "%{$term}%");
                    });
                });
            })
            ->withTrashed()
            ->with('workShifts')
            ->orderBy('id', 'desc');
            
            if( $paginate )
                $users = $users->simplePaginate(25)->withQueryString();
            else
                $users = $users->take(100)->get();

            return response()->json([
                'success'   => true,
                'data'      => $users,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email:rfc|unique:users,email',
            'username'      => 'required|unique:users,username',
            'password'      => 'required|min:8',
            'work_shifts'   => 'required|array|min:1'
        ]);

        $error_msg = null;
        $did_succed = false;
        $created_user = null;

        try
        {
            DB::beginTransaction();

            $user_data      = $request->all();
            $created_user   = User::create( $user_data );

            // Sync workshifts
            $created_user->workShifts()->sync($user_data['work_shifts']);

            // Associate permissions
            $permissions    = array_keys($user_data['permissions'] ?? []);
            $created_user->syncPermissions( $permissions );

            AppLogger::log("Nueva cuenta de usuario creado", $created_user);

            DB::commit();
            $did_succed = true;
        }
        catch(\ErrorException $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }
        catch(\Exception $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }

        return response()->json([
            'success'   => $did_succed,
            'message'   => $did_succed ? "Usuario agregado!" : $error_msg,
            'data'      => $created_user
        ], $did_succed ? 201 : 500);
    }

    public function show(Request $request, $id)
    {
        $user = User::with([
            'permissions',
            'workShifts',
        ])
        ->where('id', $id)
        ->first();

        if( !$user )
            return response()->json([
                'success'   => false,
                'message'   => "El usuario #{$id} no existe!"
            ], 404);

        return response()->json([
            'success'   => true,
            'data'      => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if( !$user )
            return response()->json([
                'success'   => false,
                'message'   => "El usuario #{$id} no existe!"
            ], 404);

        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => "required|email:rfc|unique:users,email,{$id}",
            'username'          => "required|unique:users,username,{$id}",
            'change_password'   => 'nullable',
            'password'          => 'required_with:change_password|min:8',
            'avatar'            => 'nullable|file|mimes:jpg,bmp,png,gif,jpeg,webp',
            'work_shifts'       => 'required|array|min:1'
        ]);
        
        $user_data  = $request->all();
        $did_succed = false;
        $error_msg  = null;

        try
        {
            DB::beginTransaction();

            // check for avatar file
            if( isset( $user_data['avatar']) ){

                // Update file
                $user_data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update( $user_data );

            // Sync workshifts
            $user->workShifts()->sync($user_data['work_shifts']);

            // Associate permissions
            if( isset($user_data['permissions']) )
            {
                $permissions    = array_keys($user_data['permissions']);
                $user->syncPermissions( $permissions );
            }

            AppLogger::log("Cuenta de usuario #{$id} actualizada", $user);

            DB::commit();
            $did_succed = true;
        }
        catch(\ErrorException $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }
        catch(\Exception $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }

        return response()->json([
            'success'   => $did_succed,
            'message'   => $did_succed ? "Usuario actualizado!" : $error_msg,
            'data'      => $user
        ], $did_succed ? 200 : 500 );
    }

    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$user )
            return response()->json([
                'success'   => false,
                'message'   => "La cuenta del usuario #{$id} no existe!"
            ], 404);

        $user->restore();

        AppLogger::log("Cuenta de usuario #{$id} re-activada", $user);

        return response()->json([
            'success'   => true,
            'message'   => 'Cuenta de usuario re-activada!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if( !$user )
            return response()->json([
                'success'   => false,
                'message'   => "El usuario #{$id} no existe!"
            ], 404);
        
        $user->delete();

        AppLogger::log("Cuenta de usuario #{$id} archivada", $user);

        return response()->json([
            'success'   => true,
            'message'   => 'Cuenta de usuario archivada!'
        ]);
    }
}
