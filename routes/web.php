<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontControlador;
use App\Http\Controllers\ComandaControlador;

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
    return view('inicio');
   
});

Route::get('comandas/ultima', [ComandaControlador::class, 'ultima'])->name('comandas/ultima');
Route::get('comandas/cola', [ComandaControlador::class, 'cola'])->name('comandas/cola');
Route::get('comandas/cola', [ComandaControlador::class, 'cola'])->name('comandas/cola');
Route::get('cola/procesar/todas', [ComandaControlador::class, 'procesarTodas'])->name('cola/procesar/todas');
Route::resource('comandas', ComandaControlador::class);

