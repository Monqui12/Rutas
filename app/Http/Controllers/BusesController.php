<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buse;

class BusesController extends Controller
{
    public function save(Request $request){
        Buse::create([
            'placa'=> $request->placa,
            'cantidad'=> $request->cantidad
        ]);
        return ['status'=> 200, 'msg'=> "Exito"];
    }
    public function leer(){
        $oBuses = Buse::all();
        return ['buses'=> $oBuses];
    }
}
