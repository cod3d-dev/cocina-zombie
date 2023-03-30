<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class Platos
{
     // function obtenerComanda
     // Devuelvo un array con la comanda */
     public static function cargarJSON()
    {

        if (file_exists(storage_path("/platos.json"))) {
      
            $platos = json_decode(file_get_contents(storage_path() . "/platos.json"), true);
            
            $nuevosPlatos = 0;
            foreach ($platos as $plato) {

                $existe = Redis::sismember('platos:lista', $plato['id']);
                if (!$existe) {
                    $agregarPlato = Redis::hset('platos:' . $plato['id'], 'id', $plato['id']);
                    $agregarPlato = Redis::hset('platos:' . $plato['id'], 'nombre', $plato['nombre']);
                    $agregarPlato = Redis::hset('platos:' . $plato['id'], 'tipo', $plato['tipo']);
                    $agregarPlatoLista = Redis::sadd('platos:lista', $plato['id']);
                    $nuevosPlatos++;
                }
            }

            if($nuevosPlatos>0) {
                return redirect()->back()->with('tipoMensaje', 'success')->with('mensaje', '¡Se agregaron ' . $nuevosPlatos . ' nuevos platos!');
            } else {
                return redirect()->back()->with('tipoMensaje', 'warning')->with('mensaje', '¡No hay nuevos platos en el archivo!');
            }

        } else {
            return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡No se encontró el archivo!');
        }
    }
    /* end function cargarPlatos */
    

    // Obtener lista de todos los platos
    public static function ListaPlatos() {
          $platos = false;
          $listaPlatos = Redis::smembers('platos:lista');

          if (!empty($listaPlatos)) {
               for ($i = 0; $i < count($listaPlatos); $i++) {
                    $platos[$i] = Redis::hgetall('platos:' . $listaPlatos[$i]);
               }
          }
          return $platos;
     }


    // Obtengo los datos del plato
    public static function obtener($id)
    {
        $plato = Redis::hgetall('platos:' . $id);
        return $plato;
    }
}
