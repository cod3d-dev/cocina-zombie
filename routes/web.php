<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
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
    // return view('welcome');
    $visits = Redis::incrby('visits.5.downloads',5);

    echo $visits;
});

Route::get('/nuevopedido', function () {
    // return view('welcome');
    $fecha = time();

    $totalcomandas = Redis::incr('totalcomandas');

    // dd($totalcomandas);
    
    Redis::hset('comandas:' . $totalcomandas, 'fecha', $fecha);
    Redis::hset('comandas:' . $totalcomandas, 'cliente', "nombre");
    
    // $visits = Redis::incrby('visits.5.downloads',5);

    // dd($fecha);
});

Route::get('/ultimacomanda', function () {
    // return view('welcome');
    $fecha = time();

    $totalcomandas = Redis::get('totalcomandas');

    // dd($totalcomandas);
    
    $fecha = Redis::hget('comandas:' . $totalcomandas, 'fecha');
    $cliente = Redis::hget('comandas:' . $totalcomandas, 'cliente');

    $nuevafecha = date('Y-m-d H:i:s', $fecha);
    $timezone_from = 'America/Caracas';
    $newDateTime = new DateTime($nuevafecha); 
    $newDateTime->setTimezone(new DateTimeZone($timezone_from)); 
    $dateTimeUTC = $newDateTime->format("Y-m-d h:i A");
    //$fecha2 = new DateTime($fecha);
    
    // $visits = Redis::incrby('visits.5.downloads',5);

    echo $dateTimeUTC;
});
