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
    public function actualizar($id){
        try {
            $oBuses = Buse::find($id);
            if($oBuses->habilitado == 1){
                $oBuses->habilitado = 0;
            }
            if($oBuses->habilitado == 0){
                $oBuses->habilitado = 1;
            }
            $oBuses->save();
            return ['status'=> 200, 'msg'=> 'Se actualizo con exito!'];
        } catch (\Throwable $th) {
            return ['status'=> 300, 'msg'=> 'Error de sistemas!', 'error'=> $th->getMessage()];
            
        }
    }
}
