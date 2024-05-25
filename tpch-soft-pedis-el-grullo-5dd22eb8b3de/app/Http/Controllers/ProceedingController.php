<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use App\Helpers\AppLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ProceedingController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $paginate           = $request->paginate;
            $filters            = json_decode($request->filters);
            $pagination         = json_decode($request->pagination);
            $term               = $filters->term;

            $records = Proceeding::with([
                'citizen'
            ])
            ->withCount('records')
            ->when($filters->citizen, function($q)use($filters){
                $q->whereHas('citizen', function($sq)use($filters){
                    $sq->where('id', $filters->citizen->id);
                });
            })
            ->when(true, function($q)use($filters){
                // Age filters
                $q->whereHas('citizen', function($sq)use($filters){

                    if( $filters->minAge && !$filters->maxAge )
                        $sq->where('age', '>=', $filters->minAge);
                    elseif( !$filters->minAge && $filters->maxAge )
                        $sq->where('age', '<=', $filters->maxAge);
                    elseif( $filters->minAge && $filters->maxAge )
                        $sq->whereBetween('age', [
                            $filters->minAge,
                            $filters->maxAge
                        ]);
                });
                // Status filters
                if( isset($filters->status) && $filters->status !== '' )
                    $q->where('status', $filters->status);
            })
            ->where(function($q)use($term){
                $q
                ->whereHas('citizen', function($sq)use($term){
                    $sq->where(function($ssq)use($term){
                        $ssq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('second_last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('address_street', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"])
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name, ' ', second_last_name) LIKE ?", ["%{$term}%"]);
                    });
                })
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%");
            })
            ->withTrashed()
            ->orderBy('id', 'desc');

            $total_records = 0;

            if( $paginate )
            {
                $total_records  = (clone $records)->count();
                $records        = $records->simplePaginate(15);
            }
            else
                $records = $records->take(100)->get();

            return response()->json([
                'success'   => true,
                'data'      => $records,
                'total'     => $total_records,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'citizen_id'    => 'required|exists:citizens,id',
            'name'          => 'required',
            'description'   => 'nullable',
            'tags'          => 'nullable',
            'status'        => 'nullable',
        ]);

        try
        {
            DB::beginTransaction();

            // Create Records
            $created = Proceeding::create([
                'citizen_id'    => $request->citizen_id,
                'name'          => $request->name,
                'description'   => $request->description,
                'tags'          => $request->tags,
                'status'        => $request->status,
            ]);

            AppLogger::log(
                "Expediente registrado",
                $created,
            );

            DB::commit();
            $did_succed = true;
        }
        catch(Exception $ex)
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
            'message'   => $did_succed ? 'Expediente registrado con exito!' : $error_msg,
            'data'      => $created,
        ], $did_succed ? 201 : 500);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'data.name'          => 'required',
            'data.description'   => 'nullable'
        ]);

        try
        {
            DB::beginTransaction();

            // Create Records
            $proceeding = Proceeding::find($id);
            if( !$proceeding )
                return response()->json([
                    'success'   => false,
                    'message'   => 'El expediente no existe!',
                ], 404);

            $proceeding->update([
                'name'          => $request->data['name'],
                'description'   => $request->data['description'],
            ]);

            AppLogger::log(
                "Expediente actualizado",
                $request->data,
            );

            DB::commit();
            $did_succed = true;
        }
        catch(Exception $ex)
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
            'message'   => $did_succed ? 'Expediente actualizado con exito!' : $error_msg,
            'data'      => $request->data,
        ], $did_succed ? 200 : 500);
    }

    public function show(Request $request, $id)
    {
        $proceeding = Proceeding::withTrashed()
        ->with('citizen')
        ->where('id', $id)
        ->first();

        $term = $request->term;

        if( !$proceeding )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

        if( $request->with_proceeding_records == 1){

            $logged_user = $request->user();

            // Add citizen proceeding records
            $proceeding->records = $proceeding
            ->records()
            ->withTrashed()
            ->with([
                'citizen',
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
                        $sq->whereIn(
                            'work_shift_id',
                            $logged_user->workShifts->pluck('id')->toArray()
                        );
                    });
                }
            })
            ->simplePaginate(10)
            ->withQueryString();
        }

        return response()->json([
            'success'   => true,
            'data'      => $proceeding,
        ]);
    }

    public function showExpiringCases(Request $request, $id)
    {
        $proceeding = Proceeding::withTrashed()
            ->with('citizen')
            ->where('id', $id)
            ->first();

        $term = $request->term;

        if( !$proceeding )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

        if( $request->with_proceeding_records == 1){

            $logged_user = $request->user();

            // Add citizen proceeding records
            $proceeding->records = $proceeding
                ->records()
                ->withTrashed()
                ->with([
                    'citizen',
                    'proceedingTemplate',
                    'user'
                ])
                ->orderBy('id', 'desc')
                ->where(function ($query) {
                    $query->where('description', 'like', '%TIEMPO: 60 DÍAS%')
                        ->orWhere('description', 'like', '%TIEMPO: 30 DÍAS%')
                        ->orWhere('description', 'like', '%TIEMPO: 15 DÍAS%');
                })
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
                            $sq->whereIn(
                                'work_shift_id',
                                $logged_user->workShifts->pluck('id')->toArray()
                            );
                        });
                    }
                })
                ->simplePaginate(10)
                ->withQueryString();
        }

        return response()->json([
            'success'   => true,
            'data'      => $proceeding,
        ]);
    }

    public function restore(Request $request, $id)
    {
        $record = Proceeding::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

        $record->restore();

        AppLogger::log(
            "Expediente #{$id} desarchivado",
            $record,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Expediente desarchivado!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $record = Proceeding::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

        $record->delete();

        AppLogger::log(
            "Expediente #{$id} archivado",
            $record,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Expediente archivado!'
        ]);
    }

    public function open(Request $request, $id)
    {
        $record = Proceeding::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

        $record->update([
            'status' => 'Abierto',
        ]);

        AppLogger::log(
            "Expediente #{$id} re-abierto",
            $record,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Expediente re-abierto!'
        ]);
    }

    public function close(Request $request, $id)
    {
        $record = Proceeding::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El expediente #{$id} no existe!"
            ], 404);

            $record->update([
                'status' => 'Cerrado',
            ]);

            AppLogger::log(
                "Expediente #{$id} cerrado",
                $record,
            );

        return response()->json([
            'success'   => true,
            'message'   => 'Expediente cerrado!'
        ]);
    }
}
