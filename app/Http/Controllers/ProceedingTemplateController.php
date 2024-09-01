<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProceedingTemplate;
use App\Models\ProceedingTemplateField;
use Illuminate\Database\QueryException;
use App\Helpers\AppLogger;
use DB;

class ProceedingTemplateController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $paginate   = $request->paginate;
            $only_active= $request->onlyActive;
            $filters    = json_decode($request->filters);
            $pagination = json_decode($request->pagination);
            $term       = $filters->term;

            $templates = ProceedingTemplate::where(function($q)use($term){
                $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('workShift', function($sq)use($term){
                    $sq->where('name', 'like', "%{$term}%");
                });
            })
            ->withCount(['fields'])
            ->with('workShift')
            ->orderBy('name');

            if( !$only_active )
                $templates->withTrashed();
            
            if( $paginate )
                $templates = $templates->simplePaginate(15);
            else
                $templates = $templates->take(100)->get();

            return response()->json([
                'success'   => true,
                'data'      => $templates
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'data'                          => 'required',
            'data.name'                     => 'required',
            'data.work_shift_id'            => 'required|exists:work_shifts,id',
            'data.proceedingTemplateFields' => 'required|array|min:1',
        ]);

        $template           = $request->data;
        $save_succed        = false;
        $exception_message  = null;

        DB::beginTransaction();

        try
        {
            // Create template
            $created_template = ProceedingTemplate::create([
                'name'          => $template['name'],
                'description'   => $template['description'],
                'work_shift_id' => $template['work_shift_id'],
                'tag_version'   => 1,
            ]);
            
            $fields = $template['proceedingTemplateFields'];
            foreach( $fields as $idx => $field )
            {
                unset($field['id']);
                $field['order_index'] = $idx;
                $created_template->fields()->create( $field );
            }

            AppLogger::log(
                "Nuevo formato creado",
                $created_template
            );

            $save_succed = true;
            DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollback();
            $exception_message = $ex->getMessage();
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            $exception_message = $ex->getMessage();
        }

        return response()->json([
            'success'   => $save_succed,
            'message'   => $save_succed ? 'Plantilla guardada!' : $exception_message ,
        ], $save_succed ? 200 : 500);
    }

    public function show(Request $request, $id)
    {
        $template = ProceedingTemplate::withTrashed()
        ->where('id', $id)
        ->first();
        
        if( !$template )
            return response()->json([
                'success'   => false,
                'message'   => "El formato #{$id} no existe!"
            ], 404);

        return response()->json([
            'success'   => true,
            'data'      => $template->load([
                'fields' => function($q){
                    $q->orderBy('order_index');
                }
            ])
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = ProceedingTemplate::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$template )
            return response()->json([
                'success'   => false,
                'message'   => "El formato #{$id} no existe!"
            ], 404);

        $request->validate([
            'data.name'                     => 'required',
            'data.work_shift_id'            => 'required|exists:work_shifts,id',
            'data.proceedingTemplateFields' => 'required|array|min:1',
        ]);

        $template_data      = $request->data;
        $save_succed        = false;
        $exception_message  = null;
        $original_fields    = $template->fields->toArray();

        DB::beginTransaction();

        try
        {
            // Save current version in logs
            $template->versionLogs()->create([
                'name'         => $template->getOriginal('name'),
                'description'  => $template->getOriginal('description'),
                'tag_version'  => $template->getOriginal('tag_version'),
                'fields'       => $original_fields,
            ]);

            // Create template
            $template->update([
                'name'          => $template_data['name'],
                'description'   => $template_data['description'],
                'work_shift_id' => $template_data['work_shift_id'],
                'tag_version'   => $template->tag_version+1,
            ]);
            
            $fields = $template_data['proceedingTemplateFields'];
            $ids_to_update = collect( $fields )
            ->pluck('id')
            ->filter(function($id){
                return is_numeric( $id );
            })->toArray();

            // Delete fields no loger existing
            $template->fields()->whereNotIn(
                'id', 
                $ids_to_update
            )->delete();

            foreach( $fields as $idx => $field )
            {

                $field['order_index'] = $idx;

                if( is_numeric($field['id']) )
                {
                    // update
                    $template->fields()
                    ->find($field['id'])
                    ->update( $field );
                }
                else
                {
                    // create
                    $template->fields()
                    ->create( $field );
                }
            }

            AppLogger::log(
                "Formato #{$id} actualizado",
                $template
            );

            $save_succed = true;
            DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollback();
            $exception_message = $ex->getMessage();
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            $exception_message = $ex->getMessage();
        }

        return response()->json([
            'success'   => $save_succed,
            'message'   => $save_succed ? 'Plantilla actualizada!' : $exception_message ,
        ], $save_succed ? 200 : 500);
    }

    public function restore(Request $request, $id)
    {
        $template = ProceedingTemplate::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$template )
            return response()->json([
                'success'   => false,
                'message'   => "El formato #{$id} no existe!"
            ], 404);

        $template->restore();

        AppLogger::log(
            "Formato #{$id} activado",
            $template
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Formato activado!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $template = ProceedingTemplate::withTrashed()
        ->where('id', $id)
        ->first();

        if( !$template )
            return response()->json([
                'success'   => false,
                'message'   => "El formato #{$id} no existe!"
            ], 404);

        $template->delete();

        AppLogger::log(
            "Formato #{$id} desactivado",
            $template
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Formato desactivado!'
        ]);
    }
}
