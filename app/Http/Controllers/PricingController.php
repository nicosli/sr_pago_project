<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addresses;
use Validator;
use GuzzleHttp\Client;


class PricingController extends Controller
{
    /**
     * Retrieve gas pricing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function get(Request $request){

        $error = false;
        $results = "";
        $errorMessage = "";
        $codeResponse = 200;
        $gasLocations = "";

        // rules to validate the request
        $rules = [
            'county' => 'required|max:255',
            'municipality' => 'max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        
        // if the validator is true
        if ($validator->passes()) {

            // query to get all locations with county
            $addresses = Addresses::where('d_estado', '=', '%:county%');
            
            // if municipality is not empty, then add the where query
            if( ! empty($request->get('municipality')) ){
                $addresses->where('D_mnpio', '=', '%:municipality%');
            }

            // set bindings to prevent SQL Inject atack
            $addresses->setBindings([
                'county' => $request->get('county'),
                'municipality' => ! empty ($request->get('municipality'))? $request->get('municipality') : ''
            ]);
            $addresses->limit(10);
            // Retrieve the data from database
            $addresses = $addresses->get();

            // if the query has results
            if(count($addresses) > 0){
                
                try {
                    $client = new Client([ 'base_uri' => config('services.API_GAS') ]);
                    $response = $client->request('GET', '/v1/precio.gasolina.publico?pageSize=10018');
                    
                    // Clean Byte Order Mark before json decode
                    $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody());
                    $gasLocations = json_decode($json, true);
                    $gasLocations = $gasLocations['results'];

                    // Merge both results
                    $results = Self::getLocations($addresses, $gasLocations);
                } catch (\Throwable $th) {
                    $errorMessage = "Error internal " . $th;
                    $codeResponse = 500;
                }

            } else {
                // if the query result is empty
                $results = "";
                $codeResponse = 204;
            }

        } else {
            $error = true;
            $errorMessage = $validator->errors()->all();
        }

        return response()->json([
            "error" => $error,
            "message" => $errorMessage,
            "results" => $results
        ], $codeResponse );
    }

    /**
     * group cp and search in gas locations
     *
     * @param array
     * @return array
     */
    public static function getLocations($addresses, $gasLocations){
        // codigopostal array
        $cp = [];
        $filteredAddreses = [];
        $gasLocationsResults = [];

        // Loop to reduce the codigopostal (group by d_codigo) 
        foreach ($addresses as $address) {
            if( ! in_array($address->d_codigo, $cp, true) ){
                $cp_filtered = $address->d_codigo;
                $cp[] = $cp_filtered;

                // Loop to get gas locations only the cp filtered
                foreach($gasLocations as $location){
                    if($location['codigopostal'] == $cp_filtered)
                        $gasLocationsResults[] = [
                            "id" => $location['_id'],
                            "rfc" => $location['rfc'],
                            "razonsocial" => $location['razonsocial'],
                            "date_insert" => $location['date_insert'],
                            "numeropermiso" => $location['numeropermiso'],
                            "fechaaplicacion" => $location['fechaaplicacion'],
                            "longitude" => $location['longitude'],
                            "latitude" => $location['latitude'],
                            "permisoid" => $location['permisoid'],
                            "calle" => $location['calle'],
                            "colonia" => $location['colonia'],
                            "municipio" => $address->D_mnpio,
                            "estado" => $address['d_estado'],
                            "regular" => $location['regular'],
                            "premium" => $location['premium'],
                            "dieasel" => $location['dieasel'],
                        ];
                }

            }
        }

        return $gasLocationsResults;
    }
}