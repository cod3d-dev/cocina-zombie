<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ComandaControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comandas.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required',
        ]);

        $numComanda = Redis::incr('comandas:correlativo');

        $fecha = time();


        $comanda = Redis::hset('comandas:' . $numComanda, 'cliente', $request->cliente);
        $comanda = Redis::hset('comandas:' . $numComanda, 'fecha', $fecha); 
        
        return redirect('/')->with('success', '¡Comanda creada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function ultima()
    {
        $ultimaComanda = Redis::get('comandas:correlativo');
        $comanda = $this->obtenerComanda($ultimaComanda);
        
        return view('comandas.mostrar', compact('comanda'));
    }

    public function ultimaComanda()
    {
        $ultimaComanda = -1;
        $ultimaComanda = Redis::get('comandas:correlativo');
        
        $comanda = $this->obtenerComanda($ultimaComanda);

        

        return $comanda;
    

    }

    public function obtenerComanda($id) {
        
        $comanda = Redis::hgetall('comandas:' . $id);
        return $comanda;

    }
}
