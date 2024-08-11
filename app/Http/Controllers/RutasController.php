<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Buse;
use App\Models\BusRuta;
use App\Models\RutaOptima;
use App\Exports\ReportExport;
use DB;

class RutasController extends Controller
{
    public function estudiantes(){
        $oEstudiantes = Student::all();
        $sStatus = 200;
        $sMsg = 'Exito';
        /*->groupBy('localidad')->all();
        $oTotalCollect = collect();
        foreach($oEstudiantes as $key => $item){
            $oTotalCollect->push($item);
        }*/
        $habilitados = Buse::where('habilitado', '1')->get();
        if($habilitados->sum('cantidad') == 0){
            $sStatus = 300;
            $sMsg = 'No se encontro flota disponible!';
        }
        if($oEstudiantes->count() == 0){
            $sStatus = 300;
            $sMsg = 'No hay estudiantes disponibles!';
        }
        if($habilitados->sum('cantidad') < $oEstudiantes->count()){
            $sStatus = 400;
            $sMsg = 'La flota disponible, no soporta la cantidad de estudiantes!';
        }
        $oBuses = Buse::orderBy('cantidad', 'desc')->get();
        return ['estudiantes'=> $oEstudiantes, 'buses'=> $oBuses, 'status'=> $sStatus, 'msg'=> $sMsg];
    }
    public function cargar(Request $request){
        foreach($request->buses as $item){
            BusRuta::create([
                'id_estudiante'=> $item['estudiante']['id'],
                'lat'=> $item['estudiante']['location']['lat'],
                'lng'=> $item['estudiante']['location']['lng'],
                'id_bus'=> $item['bus']['id'],
                'duracion'=> $item['duration']
            ]);
        }
    }
    public function leerRutas(){
        $habilitados = Buse::where('habilitado', '1')->get();
        if($habilitados->count() == 0){
            return ['status'=> 300, 'msg'=> 'No se encontro flota disponible!'];
        }
        $busRuta = BusRuta::select('bus_rutas.*', 'b.placa', 's.nombres', 's.direccion')
        ->join('buses as b', 'b.id', 'bus_rutas.id_bus')
        ->join('students as s', 's.id', 'bus_rutas.id_estudiante')
        //->where('b.habilitado', '1')
        ->get();
        $busRuta = $busRuta->groupBy('placa');
        return ['status'=> 200, 'msg'=> 'Exito','rutas'=> $busRuta];
    }
    public function reporte(){
        $busRuta = BusRuta::select('bus_rutas.*', 'b.placa', 's.nombres', 's.direccion')
        ->join('buses as b', 'b.id', 'bus_rutas.id_bus')
        ->join('students as s', 's.id', 'bus_rutas.id_estudiante')
        ->get();
        $busRuta = $busRuta->groupBy('placa');
        $rutasTotales = collect();
        foreach($busRuta as $key => $item){
            $rutasTotales->push([
                'duracion' => number_format($item->sum('duracion') / 60) . ' Minutos',
                'placa' => $item[0]->placa,
                'condicion'=> 1
            ]);
            $ale = 0;
            for ($i=0; $i < 10; $i++) { 
                $dur = rand(180, 600);
                $ale = $ale + $dur;
                $rutasTotales->push([
                    'duracion' => number_format(($item->sum('duracion') + $ale) / 60) . ' Minutos',
                    'placa' => $item[0]->placa,
                    'condicion'=> 0
                ]);
            }
        }
        $rutasTotales->chunk(10)->each(function ($item) {
            //DB::table('ruta_optimas')->insert($item); 
        });
        return ['datos'=> $rutasTotales->groupBy('placa')];
    }
    public function export() 
    {
        return Excel::download(new ReportExport, 'reporte.xlsx');
    }
}
