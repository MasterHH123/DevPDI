<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AppLogger;
use DB;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function index(){

        $locations = Location::all();

        return response()->json($locations);
    }

    public function store(Request $request){
        $incomingData = $request->input('data');

        \Log::info(print_r($incomingData, true));
        if(!$incomingData) {
            return response()->json([
                'message' => 'Invalid or missing GPS data.'
            ], 422);
        }

        //Transform date and time format from dmy to ymd and His.0 to His respectively
        $date = \Carbon\Carbon::createFromFormat('dmy', $incomingData['date'])->format('Y-m-d');
        $time = \Carbon\Carbon::createFromFormat('His',substr($incomingData['time'], 0, 6))->format('H:i:s');

        $incomingData['date'] = $date;
        $incomingData['time'] = $time;

        $locationStructure = [
            'citizen_id' => 'required|exists:citizens,id',
            'latitude' => 'required|between:0,99999.999999',
            'latitude_direction' => 'required|in:N,S',
            'longitude' => 'required|between:0,99999.999999',
            'longitude_direction' => 'required|in:E,W',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'altitude' => 'required|numeric',
            'speed' => 'required|numeric',
            'course' => 'required|numeric',
            'time_interval' => 'sometimes|numeric'
        ];

        $validatedData = validator($incomingData, $locationStructure)->validate();
        $existingLocation = Location::where('citizen_id', $incomingData['citizen_id'])->first();

        $did_succeed = false;
        $created = null;
        $error_msg = '';

        try
        {
            DB::beginTransaction();

            if($existingLocation) {
                $existingLocation->update($validatedData);
                $created = $existingLocation;
            } else {
                $created = Location::create($validatedData);
            }

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
        $locationJoinCitizen = DB::table('locations')
            ->leftJoin('citizens', 'locations.citizen_id', '=', 'citizens.id')
            ->select('citizens.first_name', 'citizens.last_name' ,'locations.latitude', 'locations.latitude_direction', 'locations.longitude', 'locations.longitude_direction', 'locations.date', 'locations.time', 'locations.altitude')
            ->get();

        #\Log::info(print_r($locationJoinCitizen, true));

        if(!$locationJoinCitizen){
            return response() -> json([
                'error' => 'Location not found', 404
            ]);
        } else {
            return response() -> json($locationJoinCitizen);
        }
    }


}
