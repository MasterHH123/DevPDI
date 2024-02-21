<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AppLogger;
use DB;
use App\Models\Location;


class LocationController extends Controller
{
    public function store(Request $request){
        $incomingData = $request->input('data');

        $gpsData = json_decode($incomingData, true);

        if(!$gpsData) {
            return response()->json([
                'message' => 'Invalid or missing GPS data.'
            ], 422);
        }

        $validatedData = validator($gpsData, [
            'citizen_id' => 'required|exists:citizens,id',
            'latitude' => 'required|numeric',
            'latitude_direction' => 'required|in:N,S',
            'longitude' => 'required|numeric',
            'longitude_direction' => 'required|in:E,W',
            'date' => 'required|date_format:dmy',
            'time' => 'required|date_format:His',
            'altitude' => 'required|numeric',
            'speed' => 'required|numeric',
            'course' => 'required|numeric',
            'time_interval' => 'sometimes|numeric'
        ])->validate();

        $did_succeed = false;
        $created = null;
        $error_msg = '';

        try
        {
            DB::beginTransaction();

            $created = Location::create($validatedData);

            AppLogger::log(
                "Ubicacion registrada",
                ['id' => $created->id]
            );

            DB::commit();
            $did_succeed = true;
        } catch(\ErrorException $ex) {
            DB::rollback();
            $error_msg = $ex->getMessage();
        } catch(\Exception $ex) {
            DB::rollback();
            $error_msg = $ex->getMessage();
        } catch(QueryException $ex) {
            DB::rollback();
            $error_msg = $ex->getMessage();
        }

        return response()->json([
            'success' => $did_succeed,
            'message' => $did_succeed ? 'Ubicacion registrada con exito!' : $error_msg,
            'data' => $created
        ], $did_succeed ? 201 : 500);
    }

    public function show(Request $request){
        $location = Location::query()
            ->select('latitude', 'latitude_direction', 'longitude', 'longitude_direction', 'time', 'altitude')
            ->groupBy('citizen_id')
            ->get();

        if(!$location){
            return response() -> json([
                'error' => 'Location not found', 404
            ]);
        } else {
            return response() -> json($location);
        }
    }


}
