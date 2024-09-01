<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Citizen;
use App\Models\Proceeding;
use App\Models\ProceedingRecord;
use App\Models\ProceedingTemplate;

class PanelController extends Controller
{
    public function home(Request $request)
    {
        $user = $request->user();

        return view('layouts.app', [
            'user'  => $user->load([
                'permissions',
                'workShifts',
            ])
        ]);
    }

    public function stats()
    {
        $dashboard_data = [
            'citizens'    => [
                'total'         => Citizen::count(), 
                'male_count'    => Citizen::male()->count(),
                'female_count'  => Citizen::female()->count(),
            ],
            'proceedings' => [
                'total'         => Proceeding::count(),
                'male_count'    => Proceeding::whereHas('citizen', function($q){
                    $q->male();
                })->count(),
                'female_count'  => Proceeding::whereHas('citizen', function($q){
                    $q->female();
                })->count(),
                'undefined_count'=> Proceeding::whereHas('citizen', function($q){
                    $q->noGenre();
                })->count(),
                'latest'       => ProceedingRecord::with([
                    'proceeding',
                    'citizen', 
                    'user', 
                    'proceedingTemplate'
                ])->latest()->first(),
            ],
            'users' => [
                'total'             => User::count(),
                'deactivated_count' => User::onlyTrashed()->count()
            ]
        ];

        return response()->json([
            'success'   => true,
            'data'      => $dashboard_data
        ]);
    }
}
