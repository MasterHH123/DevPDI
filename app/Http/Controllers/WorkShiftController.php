<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{

    public function index(Request $request)
    {
        $paginate           = $request->paginate;
        $filters            = json_decode($request->filters);
        $pagination         = json_decode($request->pagination);
        $term               = $filters->term;
        $shifts     = WorkShift::when($term, function($q)use($term){
            $q->where('name', 'like', "%{$term}%");
        })->orderBy('name');

        if( $paginate == 1)
            $shifts = $shifts->simplePaginate(25);
        else
            $shifts = $shifts->get();

        return response()->json([
            'success'   => true,
            'data'      => $shifts, 
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:work_shifts',
        ]);

        WorkShift::create($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'Turno creado!', 
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|unique:work_shifts',
        ]);

        $shift = WorkShift::find($id);

        if( !$shift )
            return response()->json([
                'success'   => false,
                'message'   => 'El turno no existe!', 
            ], 404);

        $shift->update([
            'name'  => $request->name,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Turno actualizado!', 
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $shift = WorkShift::find($id);

        if( !$shift )
            return response()->json([
                'success'   => false,
                'message'   => 'El turno no existe!', 
            ], 404);

        $shift->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Turno eliminado!', 
        ]);
    }
}
