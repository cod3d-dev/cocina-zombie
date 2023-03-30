<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

use Platos;

class Cmda
{
     // function obtenerComanda
     // Devuelvo un array con la comanda */
     public static function obtener($id)
     {
          $datosComanda = self::DatosComanda($id);

          if (empty($datosComanda)) {
               return [];
          }

          $platosComanda = self::platos($id);

          $comanda = $datosComanda;
          $comanda['platos'] = $platosComanda;



          return $comanda;
     }

     // function obtenerDatosComanda
     // Obtengo los datos de la comanda de la BD */
     public static function DatosComanda($id)
     {

          $comanda = Redis::hgetall('comandas:' . $id);
          return $comanda;
     }

     // function obtenerPlatosComanda
     // Obtengo los platos asociados a la comanda de la BD */
     public static function platos($idComanda)
     {
          $platos = [];
          $lineas = Redis::hget('comandas:' . $idComanda, 'lineas');

          for ($i = 1; $i <= $lineas; $i++) {
               $idPlato = Redis::hget('comandas:' . $idComanda . ':linea:' . $i, 'id');

               $cant = Redis::hget('comandas:' . $idComanda . ':linea:' . $i, 'cant');
               $nombrePlato = Redis::hget('platos:' . $idPlato, 'nombre');


               array_push($platos, ["id" => $idPlato, "nombre" => $nombrePlato, "cant" => $cant]);
          }
          return $platos;
     }

     public static function modificar($idComanda, $campo, $valor)
     {
          $modificar = Redis::hset('comandas:' . $idComanda, $campo, $valor);

          return $modificar;
     }

     public static function agregar($datos) {
          
          $fecha = time();
          $numComanda = Redis::incr('comandas:correlativo');

          $agregar = Redis::hset('comandas:' . $numComanda, 'mesa', $datos['mesa']);
          $agregar = Redis::hset('comandas:' . $numComanda, 'createdAt', $fecha);
          $agregar = Redis::hset('comandas:' . $numComanda, 'id', $numComanda);


          // Itero entre el array de cantidades para obtener la información del plato
          $lineas = 0;

          $platos = $datos['platos'];
          for ($i = 0; $i < count($platos); $i++) {
               if ($platos[$i]['cant'] > 0) {
                    $lineas++;

                    $especial = Platos::obtener($platos[$i]['id'])['tipo'];
                  
                    $agregarLinea = Redis::hset('comandas:' . $numComanda . ':linea:' . $lineas, 'id', $platos[$i]['id']);
                    $agregarLinea = Redis::hset('comandas:' . $numComanda . ':linea:' . $lineas, 'cant', $platos[$i]['cant']);

                    if ($especial == 'especial') {
                         $comandaEspecial = true;
                    } else {
                         $comandaEspecial = false;
                    }
               }
          }

          $agregar = Redis::hset('comandas:' . $numComanda, 'lineas', $lineas);
          $agregar = Redis::hset('comandas:' . $numComanda, 'especial', $comandaEspecial);
          $agregar = Redis::hset('comandas:' . $numComanda, 'createdAtCrip', Cript::encriptar($fecha));
          
          $fechaZombie = Zombie::hackeo();
          
      
          if($fechaZombie <> false) {
               $hackeo = Redis::hset('comandas:' . $numComanda, 'createdAt', strtotime($fechaZombie));
          }
          
          $cola = Cola::agregar($numComanda, $comandaEspecial);
          
          return $agregar;
     }

     public static function ultima() {
          $ultima = Redis::get('comandas:correlativo');
          return $ultima;
     }
}
