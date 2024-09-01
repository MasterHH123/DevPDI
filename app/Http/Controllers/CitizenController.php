<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use App\Helpers\AppLogger;
use Storage;

class CitizenController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $paginate   = $request->paginate;
            $filters    = json_decode($request->filters);
            $pagination = json_decode($request->pagination);
            $term       = $filters->term;

            $citizens = Citizen::when($term, function($q)use($term){
                $q->where(function($sq)use($term){
                    $sq
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('second_last_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('address_street', 'like', "%{$term}%")
                    ->orWhere('age', 'like', "%{$term}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name, ' ', second_last_name) LIKE ?", ["%{$term}%"]);
                });
            })
            ->withTrashed()
            ->orderBy('first_name');
            
            if( $paginate )
                $citizens = $citizens->simplePaginate();
            else
                $citizens = $citizens->take(100)->get();

            return response()->json([
                'success'   => true,
                'data'      => $citizens,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'second_last_name'  => 'required',
            'gender'            => 'required|in:M,F,O',
            'address_street'    => 'required',
            'birth_date'        => 'required|date',
            'phone'             => 'nullable|size:10|unique:citizens,phone',
            'curp'              => 'nullable|size:18|unique:citizens,curp',
            'avatar'            => 'nullable|file|mimes:jpg,bmp,png,gif,jpeg,webp',
        ]);

        $citizen_data      = $request->all();

        if( isset( $citizen_data['avatar']) ){

            // Update file
            $citizen_data['avatar'] = $request->file('avatar')->store('citizen/avatars', 'public');
        }

        $created_citizen   = Citizen::create( $citizen_data );
        
        AppLogger::log(
            "Se registraron los datos del ciudadano {$request->first_name} {$request->last_name} {$request->second_last_name}",
            $created_citizen
        );

        return response()->json([
            'success'   => true,
            'message'   => "Ciudadano registrado!",
            'data'      => $created_citizen
        ]);
    }

    public function show(Request $request, $id)
    {
        $citizen = Citizen::where('id', $id)->first();
        $term = $request->term; 

        if( !$citizen )
            return response()->json([
                'success'   => false,
                'message'   => "El ciudadano #{$id} no existe!"
            ], 404);

        if( $request->with_proceeding_records == 1){

            $logged_user = $request->user();
            
            // Add citizen proceeding records
            $citizen->proceeding_records = $citizen
            ->proceedingRecords()
            ->withTrashed()
            ->with([
                'proceedingTemplate',
                'user'
            ])
            ->orderBy('id', 'desc')
            ->when($term, function($q)use($term){
                $q->where(function($sq)use($term){
                    $sq->whereHas('proceedingTemplate', function($ssq)use($term){
                        $ssq->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                    })
                    ->orWhereHas('user', function($ssq)use($term){
                        $ssq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('second_last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"])
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name, ' ', second_last_name) LIKE ?", ["%{$term}%"]);
                    });
                });
            })
            ->when(true, function($q)use($logged_user){
                
                if( !$logged_user->is_admin )
                {
                    $q->WhereHas('proceedingTemplate', function($sq)use($logged_user){
                        $sq->whereIn('work_shift', $logged_user->work_shifts);
                    });
                }
            })
            ->simplePaginate(25)
            ->withQueryString();

            // Add last proceeding record
            $citizen->last_proceeding_record = $citizen
            ->proceedingRecords()
            ->withTrashed()
            ->with([
                'proceedingTemplate',
                'user'
            ])
            ->latest()
            ->first();
        }

        return response()->json([
            'success'   => true,
            'data'      => $citizen,
        ]);
    }

    public function update(Request $request, $id)
    {
        $citizen = Citizen::find($id);

        if( !$citizen )
            return response()->json([
                'success'   => false,
                'message'   => "El ciudadano #{$id} no existe!"
            ], 404);

        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'second_last_name'  => 'required',
            'gender'            => 'required|in:M,F,O',
            'address_street'    => 'required',
            'birth_date'        => 'required|date',
            'phone'             => "nullable|size:10|unique:citizens,phone,{$id}",
            'curp'              => "nullable|size:18|unique:citizens,curp,{$id}",
            'avatar'            => 'nullable|file|mimes:jpg,bmp,png,gif,jpeg,webp',
        ]);
        
        $citizen_data      = $request->all();

        // If requested avatar deletion
        if( isset($citizen_data['did_remove_avatar']) ){
            
            Storage::disk('public')->delete( $citizen->avatar );
            $citizen_data['avatar'] = null;
        }

        if( isset( $citizen_data['avatar']) ){

            // Update file
            $citizen_data['avatar'] = $request->file('avatar')->store('citizen/avatars', 'public');
        }

        $citizen->update( $citizen_data );
        
        AppLogger::log(
            "Se actualizaron los datos del ciudadano #{$id}, {$request->first_name} {$request->last_name} {$request->second_last_name}",
            $citizen
        );

        return response()->json([
            'success'   => true,
            'message'   => "Ciudadano actualizado!",
            'data'      => $citizen
        ]);
    }

    public function restore(Request $request, $id)
    {
        $citizen = Citizen::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$citizen )
            return response()->json([
                'success'   => false,
                'message'   => "El registro del ciudadano #{$id} no existe!"
            ], 404);

        $citizen->restore();
        AppLogger::log(
            "Se desarchivo el registro del ciudadano #{$id}, {$citizen->first_name} {$citizen->last_name} {$citizen->second_last_name}"
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Registro de ciudadano desarchivado!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $citizen = Citizen::find($id);

        if( !$citizen )
            return response()->json([
                'success'   => false,
                'message'   => "El ciudadano #{$id} no existe!"
            ], 404);
        
        $citizen->delete();
        AppLogger::log(
            "Se archivo el registro del ciudadano #{$id}, {$citizen->first_name} {$citizen->last_name} {$citizen->second_last_name}"
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Registro de ciudadano archivado!'
        ]);
    }
}
