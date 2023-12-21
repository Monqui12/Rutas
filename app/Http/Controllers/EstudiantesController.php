<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Imports\EstudiantesImport;
use App\Models\Student;

class EstudiantesController extends Controller
{
    public function index(){
        return view('estudiantes');
    }
    public function cargar(Request $request){
        Excel::import(new EstudiantesImport, $request->file('file'));
    }
    public function leer(){
        $oEstudiantes = Student::all();
        return ['estudiantes'=> $oEstudiantes];
    }
}
