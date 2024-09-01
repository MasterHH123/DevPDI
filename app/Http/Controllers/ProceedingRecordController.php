<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProceedingRecord;
use App\Models\ProceedingRecordAttachment;
use Illuminate\Database\QueryException;
use App\Helpers\AppLogger;
use DB;

class ProceedingRecordController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $paginate           = $request->paginate;
            $filters            = json_decode($request->filters);
            $pagination         = json_decode($request->pagination);
            $term               = $filters->term;
            $logged_user        = $request->user();

            $records = ProceedingRecord::with([
                'citizen',
                'user',
                'proceedingTemplate' => function($q){
                    $q->withTrashed();
                }
            ])
            ->when($filters->user, function($q)use($filters){
                $q->whereHas('user', function($sq)use($filters){
                    $sq->where('id', $filters->user->id);
                });
            })
            ->when($filters->citizen, function($q)use($filters){
                $q->whereHas('citizen', function($sq)use($filters){
                    $sq->where('id', $filters->citizen->id);
                });
            })
            ->when($filters->template, function($q)use($filters){
                $q->whereHas('proceedingTemplate', function($sq)use($filters){
                    $sq->where('id', $filters->template->id);
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
                ->orWhereHas('user', function($sq)use($term){
                    $sq->where(function($ssq)use($term){
                        $ssq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('second_last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"])
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name, ' ', second_last_name) LIKE ?", ["%{$term}%"]);
                    });
                })
                ->orWhereHas('proceedingTemplate', function($sq)use($term){
                    $sq->where(function($ssq)use($term){
                        $ssq->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                    });
                })
                ->orWhere('notes', 'like', "%{$term}%");
            })
            ->when(true, function($q)use($logged_user){
                // Check user visibility allowance
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
            ->withTrashed()
            ->orderBy('id', 'desc');
            
            $total_records = 0;

            if( $paginate )
            {
                $total_records  = (clone $records)->count();
                $records        = $records->simplePaginate(25)->withQueryString();
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
            'citizen_id'                        => 'required|exists:citizens,id',
            'proceeding_id'                     => 'required|exists:proceedings,id',
            'proceeding_template_id'            => 'required|exists:proceeding_templates,id',
            'proceeding_template_tag_version'   => 'required',
            'proceeding_template_field_ids'     => 'required',
            'attachments'                       => 'nullable|array',
            'attachments.*'                     => 'file|mimes:'.implode(',',ProceedingRecordAttachment::ALLOWED_EXT)
        ]);

        $did_succed = false;
        $error_msg  = null;

        try
        {
            DB::beginTransaction();
            // Create Records
            $created_record = ProceedingRecord::create([
                'proceeding_id'                     => $request->proceeding_id,
                'proceeding_template_id'            => $request->proceeding_template_id,
                'proceeding_template_tag_version'   => $request->proceeding_template_tag_version,
                'citizen_id'                        => $request->citizen_id,
                'user_id'                           => $request->user()->id,
                'notes'                             => $request->notes,
            ]);

            // Then, create record field-entries
            $entries = $request->proceeding_template_field_ids;
            foreach($entries as $proceeding_template_field_id => $field_value){
                
                $created_record->fieldEntries()->create([
                    'proceeding_template_field_id'  => $proceeding_template_field_id,
                    'field_value'                   => $field_value,
                ]);
            }

            // Save attachments
            $attachments = $request->attachments ?? [];
            foreach($attachments as $attachment){
                
                $path = $attachment->store(
                    'precords/'.$created_record->id.'/attachments',
                    'public'
                );

                $created_record->attachments()->create([
                    'name'          => $attachment->getClientOriginalName(),
                    'file_ext'      => $attachment->getClientOriginalExtension(),
                    'path'          => $path,
                ]);
            }

            AppLogger::log(
                "Registro de expediente #{$request->proceeding_id} guardado",
                [
                    'created' => $created_record,
                    'request' => $request->all()
                ],
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
            'message'   => $did_succed ? 'Registro guardado con exito!' : $error_msg,
        ], $did_succed ? 201 : 500);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'citizen_id'                        => 'required|exists:citizens,id',
            'proceeding_id'                     => 'required|exists:proceedings,id',
            'proceeding_template_id'            => 'required|exists:proceeding_templates,id',
            'proceeding_template_tag_version'   => 'required',
            'proceeding_template_field_ids'     => 'required',
            'attachments'                       => 'nullable|array',
            'attachments.*'                     => 'file|mimes:'.implode(',',ProceedingRecordAttachment::ALLOWED_EXT)
        ]);

        $did_succed = false;
        $error_msg  = null;

        try
        {
            DB::beginTransaction();
            
            $record = ProceedingRecord::find($id);

            if( !$record )
                return response()->json([
                    'success'   => false,
                    'message'   => 'El registro no existe o fue borrado!',
                ], 404);

            $record->update([
                //'proceeding_id'                     => $request->proceeding_id,
                //'proceeding_template_id'            => $request->proceeding_template_id,
                //'proceeding_template_tag_version'   => $request->proceeding_template_tag_version,
                'user_id'                           => $request->user()->id,
                'notes'                             => $request->notes,
            ]);

            // Then, create record field-entries
            $entries = $request->proceeding_template_field_ids;
            foreach($entries as $proceeding_template_field_id => $field_value){
                $record->fieldEntries()
                ->where('proceeding_template_field_id', $proceeding_template_field_id)
                ->update([
                    'field_value'   => $field_value,
                ]);
            }

            // Delete old attachments if no longer sent in request
            $existing_attachments = collect(
                json_decode($request->existing_attachments) ?? []
            );

            $record->attachments()->whereNotIn(
                'id', 
                $existing_attachments->pluck('id')->toArray()
            )
            ->delete();

            // Save new attachments
            $attachments = $request->attachments ?? [];
            foreach($attachments as $attachment){
                
                $path = $attachment->store(
                    'precords/'.$record->id.'/attachments',
                    'public'
                );

                $record->attachments()->create([
                    'name'          => $attachment->getClientOriginalName(),
                    'file_ext'      => $attachment->getClientOriginalExtension(),
                    'path'          => $path,
                ]);
            }

            AppLogger::log(
                "Actualización de registro de expediente #{$request->proceeding_id}",
                [
                    'old' => $record->getOriginal(),
                    'new' => $request->all(),
                ],
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
            'message'   => $did_succed ? 'Registro guardado con exito!' : $error_msg,
        ], $did_succed ? 201 : 500);
    }

    public function show(Request $request, $id)
    {
        $record = ProceedingRecord::withTrashed()
        ->where('id', $id)
        ->first();
        
        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El registro #{$id} no existe!"
            ], 404);

        $logged_user = $request->user();
        if( !$logged_user->is_admin )
        {
            if(
                !in_array(
                    $record->proceedingTemplate->work_shift_id, 
                    $logged_user->workShifts->pluck('id')->toArray()
                )
            )
            {
                return response()->json([
                    'success'   => false,
                    'message'   => "No puedes ver este registro!"
                ], 404);
            }
        }

        $record->original_template = $record->getOriginalTemplate();

        return response()->json([
            'success'   => true,
            'data'      => $record->load([
                'citizen',
                'user',
                'attachments',
                'fieldEntries',
            ])
        ]);
    }

    public function restore(Request $request, $id)
    {
        $record = ProceedingRecord::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El registro #{$id} no existe!"
            ], 404);

        $record->restore();

        AppLogger::log(
            "Registro de expediente con #{$id} desarchivado",
            $record,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Registro desarchivado!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $record = ProceedingRecord::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$record )
            return response()->json([
                'success'   => false,
                'message'   => "El registro #{$id} no existe!"
            ], 404);

        $record->delete();

        AppLogger::log(
            "Registro de expediente con #{$id} archivado",
            $record,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Registro archivado!'
        ]);
    }
}
