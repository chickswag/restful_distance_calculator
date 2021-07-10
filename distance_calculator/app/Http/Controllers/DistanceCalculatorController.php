<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistanceCalculatorController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processData(Request $request){

        $arrData = $request->all();
        $rules = [
            'first_distance' => 'required',
            'second_distance'=>'required',
            'unit'=>'required'
        ];
        $validator = Validator::make($arrData, $rules);
        if ($validator->passes()) {
            try {
                if (count($arrData) == 3) {
                    $distance_all_accepted = ["meters","yards"];
                    $distance = [];
                    $requester_unit = $arrData['unit'];
                    unset($arrData['unit']);

                    foreach ($arrData as $arr_value){
                        if(is_array($arr_value)){
                            foreach ($arr_value as $unit => $value){
                                if(!in_array($unit,$distance_all_accepted)){
                                    return response()->json(["error"=>"unit value incorrect"],400);
                                }
                                else{
                                    if(is_numeric($value)){
                                        array_push($distance,["distance"=>$value,"unit"=>$unit]);
                                    }
                                    else{
                                        return response()->json(["error"=>"only numbers are permitted"],400);
                                    }
                                }
                            }
                        }
                        else{
                            return response()->json(["error"=>"unit value incorrect"],400);
                        }
                    }
                    $converted_distance = self::get_distance($requester_unit,$distance);
                    return response()->json($converted_distance." ".$requester_unit,200);
                }
                else {
                    return response()->json(["error" => "Only 3 arguments allowed and keys needs to match"]);
                }
            }
            catch (\Exception $e) {
                return response()->json(["error" => $e->getMessage()],500);
            }
        }
        else{
            return response()->json($validator->errors(), 400);
        }
    }

    /**
     * @param $unit
     * @param $data
     * @return float
     */
    public static function get_distance($unit,$data){
        $converted_numbers = [];
        foreach ($data as $value) {
            if($unit === "meters"){
                $converted_numbers[] = ($value['unit'] === "meters")? $value['distance'] : ($value['distance'] * 0.9144);
            }
            else{
                $converted_numbers[] = ($value['unit'] === "meters")? ($value['distance'] * 1.094) : $value['distance'];
            }
        }
        return round(array_sum($converted_numbers),2);
    }
}
