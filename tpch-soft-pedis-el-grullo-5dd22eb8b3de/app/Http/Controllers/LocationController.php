<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citizen;
use App\Helpers\AppLogger;
use App\Models\Location;


class LocationController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            $host = getenv('SERVER_IP');
            $port = getenv('SERVER_PORT');

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if(!$socket){
                throw new \Exception("Failed to create socket");
            }
            $connectResult = socket_connect($socket, $host, $port);
            if(!$connectResult){

                throw new \Exception("Failed to connect to socket");
            }

            $coordinatesData = socket_read($socket, 1024);
            if($coordinatesData === false){
                throw new \Exception("Failed to read data from server");
            }

            socket_close($socket);

            $decodedCoordinatesData = json_decode($coordinatesData, true);

            print $decodedCoordinatesData;

            if($decodedCoordinatesData !== null){
                Location::create($decodedCoordinatesData);

                AppLogger::log("Ubicación de ciudadano agregado", $decodedCoordinatesData);
            } else {
                return response()->json([
                    'message' => 'Invalid data received'
                ], 400);
            }
        } catch (\Exception $ex){
            $error_msg = $ex->getMessage();
        }
        return response()->json([
            'message' => 'Location received and stored',
            'data' => $decodedCoordinatesData
        ]);
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
