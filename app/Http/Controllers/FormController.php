<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addresses;
use Validator;

class FormController extends Controller
{
    /**
     * Retrieve all Countys
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public static function county(){
        return Addresses::select('d_estado')->groupBy('d_estado')->get();
    }

    /**
     * Retrieve all muncipalitys from a county
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function municipality(Request $request){
        $success = true;
        $results = "";
        $errorMessage = "";
        $codeResponse = 200;

        // rules to validate the request
        $rules = [
            'county' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        
        // if the validator is true
        if ($validator->passes()) {

            $countys = Addresses::select('D_mnpio')->where('d_estado', 'LIKE', '%:county%');
            // set bindings to prevent SQL Inject atack
            $countys->setBindings([
                'county' => $request->get('county')
            ]);
            
            $results = $countys->groupBy('D_mnpio')->get();

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
}
