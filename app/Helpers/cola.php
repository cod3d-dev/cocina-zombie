<?php

namespace App\Helpers;
use Cmda;

use Illuminate\Support\Facades\Redis;

class Cola
{
     public static function abierta() {
          $abierta = Redis::get('cocina:abierta');
  
          // Si no existe el elemento, lo creo e inicializo en true
          if (is_null($abierta)) {
              $abierta = Redis::incr('cocina:abierta');
          }
          return $abierta;
      }
  
      public static function abrir(){
          $abrir = Redis::set('cocina:abierta', 1);
          return $abrir;
      }

      public static function cerrar(){
          $cerrar = Redis::set('cocina:abierta', 0);
          return $cerrar;
      }
  
      // Agrego comanda al set que lleva la cola
      public static function agregar($idComanda, $especial)
      {
          $maxCola = Redis::get('cola:max');
  
          if (is_null($maxCola)) {
              $maxCola = Redis::incrBy('cola:max', 5);
              $maxCola = Redis::get('cola:max');
          }
  
          $maxCola = intval($maxCola);
          $tamCola = intval(Redis::zcard('cola'));
  
          // Si la cola no es igual al limite
          if ($tamCola < $maxCola) {
              $posicion = ($especial) ? 0 : $tamCola+1;
              $agregar = Redis::zadd('cola', $posicion, $idComanda);
  
  
              // Si falta 1 para llenarse la cola, cierro la cocina
              if ($tamCola == $maxCola -1) {
                  $cerrar = self::cerrar();
              }
  
              return $agregar;
          } else {
              // Cierro la cocina
              $cerrar = self::cerrar();
              return 0;
          }
      }
  
      // Carga de la BD la cola
      public static function obtener()
      {
  
          $cola = [];
          $tamCola = Redis::zCard('cola');
          $comandasCola = Redis::zRange('cola', 0, $tamCola);
  
  
  
          for ($i = 0; $i < $tamCola; $i++) {
              $comanda = Cmda::obtener($comandasCola[$i]);
              
              array_push($cola, $comanda);
          }
  
          return $cola;
      }
  
  
      // Elimino el set de la cola para procesar todas las comandas
      public static function procesarTodas()
      {
  
          $procesadas = Redis::del('cola');
          
          if($procesadas){
              $abrir = self::abrir();
              return $procesadas;
          } else {
              return false;
          }
      }
  
      public static function procesar()
      {
  
          $procesar = Redis::zpopmin('cola', 1);
          $fecha = time();

          if(!empty($procesar)){
              $comandProcesada = array_keys($procesar)[0];
              $modificar = Cmda::modificar($comandProcesada, 'dispatchedAt', $fecha);
              $abrir = self::abrir();

              $tamDespachadas = Redis::zcard('despachadas');
              
              if($tamDespachadas == 5) {
                    $eliminar = Redis::zpopmin('despachadas', 1);
              }
              
              $agregarDespachada = Redis::zAdd('despachadas', $fecha, $comandProcesada);
              
              
          }
          
      }

      // Comandas despachadas
      public static function despachadas()
      {
  
          $despachadas = [];
          $tamCola = Redis::zCard('despachadas');
          $comandasDespachadas = Redis::zRange('despachadas', 0, $tamCola);
  
  
  
          for ($i = 0; $i < $tamCola; $i++) {
              $comanda = Cmda::obtener($comandasDespachadas[$i]);
              
              array_push($despachadas, $comanda);
          }
  
          return $despachadas;
      }
}
