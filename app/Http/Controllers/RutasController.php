<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Buse;
use App\Models\BusRuta;

class RutasController extends Controller
{
    public function estudiantes(){
        $oEstudiantes = Student::all()->take(180);
        /*->groupBy('localidad')->all();
        $oTotalCollect = collect();
        foreach($oEstudiantes as $key => $item){
            $oTotalCollect->push($item);
        }*/
        $oBuses = Buse::orderBy('cantidad', 'desc')->get();
        return ['estudiantes'=> $oEstudiantes, 'buses'=> $oBuses];
    }
    public function cargar(Request $request){
        foreach($request->buses as $item){
            BusRuta::create([
                'id_estudiante'=> $item['estudiante']['id'],
                'lat'=> $item['estudiante']['location']['lat'],
                'lng'=> $item['estudiante']['location']['lng'],
                'id_bus'=> $item['bus']['id']
            ]);
        }
    }
    public function leerRutas(){
        $busRuta = BusRuta::select('bus_rutas.*', 'b.placa', 's.nombres', 's.direccion')
        ->join('buses as b', 'b.id', 'bus_rutas.id_bus')
        ->join('students as s', 's.id', 'bus_rutas.id_estudiante')
        ->get();
        $busRuta = $busRuta->groupBy('placa');
        return ['rutas'=> $busRuta];
    }
}
