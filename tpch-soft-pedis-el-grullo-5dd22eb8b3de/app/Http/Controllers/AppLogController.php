<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppLog;

class AppLogController extends Controller
{
    public function index(Request $request)
    {
        if( $request->ajax() )
        {
            $paginate   = $request->paginate;
            $filters    = json_decode($request->filters);
            $pagination = json_decode($request->pagination);
            $term       = $filters->term;

            // Date range
            $from_date       = $filters->from;
            $to_date         = $filters->to;

            $logs = AppLog::with(['relatedUser'])
            ->when($term, function($q)use($term){
                $q->where(function($sq)use($term){
                    $sq
                    ->where('description', 'like', "%{$term}%");
                });
            })
            ->when(true, function($q)use($from_date, $to_date){

                if( $from_date && !$to_date )
                    $q->whereRaw('DATE(created_at) >= ?', [$from_date]);
                elseif( !$from_date && $to_date )
                    $q->whereRaw('DATE(created_at) <= ?', [$to_date]);
                elseif( $from_date && $to_date )
                    $q->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$from_date, $to_date]);
            })
            ->orderBy('id', 'desc');
            
            if( $paginate )
                $logs = $logs->simplePaginate();
            else
                $logs = $logs->take(100)->get();

            return response()->json([
                'success'   => true,
                'data'      => $logs,
            ]);
        }
    }
}
