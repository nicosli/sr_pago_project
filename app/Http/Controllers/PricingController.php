<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CacheController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Addresses;
use Validator;


class PricingController extends Controller
{

    /**
     * Retrieve gas pricing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function get(Request $request){

        $success = true;
        $results = "";
        $errorMessage = "";
        $codeResponse = 200;
        $gasLocations = "";

        // rules to validate the request
        $rules = [
            'county' => 'required|max:255',
            'municipality' => 'max:255',
            'orderDirection' => 'required|max:4',
            'orderBy' => 'required|max:7',
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

            // Retrieve the data from database
            $addresses = $addresses->get();

            // if the query has results
            if(count($addresses) > 0){

                $gasLocations = Cache::get('gasLocations');
                if($gasLocations != null){
                    $results = Self::getLocations(
                        $addresses, 
                        $gasLocations, 
                        $request->get('orderBy'),
                        $request->get('orderDirection')
                    );
                } 

            } else {
                // if the query result is empty
                $results = "";
                $codeResponse = 204;
            }

        } else {
            $success = false;
            $errorMessage = $validator->errors()->all();
        }

        return response()->json([
            "success" => $success,
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
    public static function getLocations($addresses, $gasLocations, $orderBy, $orderDirection){
        
        // codigopostal array
        $cp = [];
        $filteredAddreses = [];
        $gasLocationsResults = [];

        // Loop to reduce the codigopostal (group by d_codigo) 
        foreach ($addresses as $address) {
            if( ! in_array($address->d_codigo, $cp, true) ){
                $cp_filtered = $address->d_codigo;
                $cp[] = $cp_filtered;

                // Loop to get gas locations (only the cp filtered)
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
        
        // Order array
        $sort = $orderDirection == 'ASC' ? SORT_ASC : SORT_DESC;
        $keys = array_column($gasLocationsResults, $orderBy);
        array_multisort($keys, $sort, $gasLocationsResults);

        return $gasLocationsResults;
    }
}