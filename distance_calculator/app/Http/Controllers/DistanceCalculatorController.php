<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Illuminate\Http\Request;

class DistanceCalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_distance(Request $request){
        $accepted_params = ["meters","yards","unit"];
        $arrData = $request->all();
        try {
            if (count($arrData) == 3) {
                $requester_unit = "";
                $distance_all_accepted = ["meters","yards"];
                $distance = [];
                $converted_numbers = [];

                if(array_key_exists('unit', $arrData)){
                    $requester_unit = $arrData['unit'];
                    unset($arrData['unit']);
                    if(!in_array($requester_unit,$distance_all_accepted)){
                        return response()->json(["error"=>"requester unit incorrect"],500);
                    }
                }
                else{
                    return response()->json(["error"=>"unit key incorrect"],500);
                }

                foreach ($arrData as  $arr_value){
                    foreach ($arr_value as $unit => $value){
                        if(in_array($unit,$distance_all_accepted)){
                            if(is_numeric($value)){
                                array_push($distance,["distance"=>$value,"unit"=>$unit]);
                            }
                            else{
                                return response()->json(["error"=>"unit value incorrect"],500);
                            }
                        }
                        else{
                            return response()->json(["error"=>"unit specified incorrect"],500);
                        }
                    }
                }
                foreach ($distance as $value) {
                    if($requester_unit === "meters"){
                            $converted_numbers[] = ($value['unit'] === "meters")? $value['distance'] : ($value['distance'] * 0.9144);
                    }
                    else{
                        $converted_numbers[] = ($value['unit'] === "meters")? ($value['distance'] * 1.094) : $value['distance'];
                    }
                }
                $final_distance_converted = round(array_sum($converted_numbers),2);
                return response()->json($final_distance_converted." ".$requester_unit,200);
            }
            else {
                return response()->json(["error" => "Only 3 arguments allowed and keys needs to match"]);
            }
        }
        catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
}
