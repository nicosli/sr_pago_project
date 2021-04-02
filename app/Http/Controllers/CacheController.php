<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CacheController extends Controller
{
    /**
     * Generate cache from API service
     *
     * @return void
     */
    public static function generate(){
        try {
            $client = new Client([ 'base_uri' => config('services.API_GAS') ]);
            $response = $client->request('GET', '/v1/precio.gasolina.publico?pageSize=10018');
            
            // Clean BOM (Byte Order Mark) before json decode
            $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody());
            $gasLocations = json_decode($json, true);
            $gasLocations = $gasLocations['results'];

            Cache::put('gasLocations', $gasLocations);

        } catch (\Throwable $th) {
            echo $th;
        }
    }
}
