<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/estudiantes', [App\Http\Controllers\EstudiantesController::class, 'index'])->name('estudiantes');
Route::post('/estudiantes/cargar', [App\Http\Controllers\EstudiantesController::class, 'cargar'])->name('estudiantes.cargar');
Route::get('/estudiantes/leer', [App\Http\Controllers\EstudiantesController::class, 'leer'])->name('estudiantes.leer');


Route::post('/buses/save', [App\Http\Controllers\BusesController::class, 'save'])->name('estudiantes.save');
Route::get('/buses/leer', [App\Http\Controllers\BusesController::class, 'leer'])->name('estudiantes.leer');
Route::get('/buses/actualizar/{id}', [App\Http\Controllers\BusesController::class, 'actualizar'])->name('estudiantes.actualizar');

Route::get('/rutas/estudiantes', [App\Http\Controllers\RutasController::class, 'estudiantes'])->name('rutas.estudiantes');
Route::post('/rutas/cargar', [App\Http\Controllers\RutasController::class, 'cargar'])->name('rutas.cargar');
Route::post('/rutas/leerRutas', [App\Http\Controllers\RutasController::class, 'leerRutas'])->name('rutas.leerRutas');
Route::get('/rutas/reporte', [App\Http\Controllers\RutasController::class, 'reporte'])->name('rutas.reporte');


