<?php

namespace App\Helpers;

Use Str;

use Illuminate\Support\Facades\Http;

class Zombie
{
     // function obtenerComanda
     // Devuelvo un array con la comanda */
     public static function hackeo() {
          
          $hackeo = false;
          $response = Http::get('https://zombie-entrando-cocina.vercel.app/api/zombie/1');
          $hackZombie = $response->json();
          
          if(array_key_exists('sw45sdf', $hackZombie )) {
              $fechaZombie = $hackZombie['sw45sdf'];
              $hackeo = Str::of($fechaZombie)->remove('$')->remove('ZOMBIEEEEEEE____');
              
          } 
          return $hackeo;
          
      }
}
