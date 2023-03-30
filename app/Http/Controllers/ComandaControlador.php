<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Redis;
use PhpParser\JsonDecoder;

use function PHPUnit\Framework\isNull;

use Cript;
use Cmda;
use Cola;
use Platos;

class ComandaControlador extends Controller
{
    
    /**
     * Formulario para crear comanda
     */
    public function create()
    {
        $platos = Platos::ListaPlatos();

        if($platos <> false) {
            return view('comandas.crear', compact('platos'));
        } else {
            return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡No existen platos!');
        }

        
    }

    /**
     * Guardar la comanda
     */
    public function store(Request $request)
    {
        
        //Esta abierta la cocina?
        if (Cola::abierta()) {

            $idPlatos = $request->plato;
            $cant = $request->cant;

            // Si la suma de todas las cantidades es mayor a 0, creo la comanda
            if (array_sum($cant)) {
            
                for ($i = 0; $i < count($cant); $i++) {
                    $datosComanda['platos'][$i]['id'] = $idPlatos[$i];
                    $datosComanda['platos'][$i]['cant'] = $cant[$i];
                }

                $datosComanda['mesa'] = $request->mesa;
                
                $agregar = Cmda::agregar($datosComanda);

                if($agregar) {
                    return redirect()->back()->with('tipoMensaje', 'success')->with('mensaje', '¡Comanda creada exitosamente!');
                }
                
            } else {
                return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡Las cantidades deben ser mayores a 0!');
            }
        } else {
            return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡Cocina Cerrada!');
        }
    }

    /**
     * Muestra la comanda
     */
    public function show(string $id)
    {
       
        $comanda = Cmda::obtener($id);
        if(!empty($comanda)){

            return view('comandas.mostrar', compact('comanda'));
        } else {
            return redirect('/')->with('tipoMensaje', 'danger')->with('mensaje', '¡Comanda no encontrada!');
        }

    }

   
    public function ultima()
    {
        
        $ultimaComanda = Cmda::ultima();
        
        if (!is_null($ultimaComanda)) {
            $comanda = Cmda::obtener($ultimaComanda);
            return view('comandas.mostrar', compact('comanda'));
        } else {
            return redirect('/')->with('tipoMensaje', 'danger')->with('mensaje', '¡No existen comandas!');
        }
        
    }

    // Muestra vista con la cola de comandas
    public function cola()
    {
        $cola = Cola::obtener();

        return view('comandas.cola', compact('cola'));
    }

    public function cola3()
    {
       return view('comandas.cola3');
    }


    public function editarComanda($id)
    {
        $comanda = $this->obtenerComanda($id);
        return view('comandas.editar', compact('comanda'));
    }
    
    public function update(Request $request){
        $fecha = $request->createdAt;
        
        $this->modificarComanda($request->idComanda, 'createdAt', strtotime($request->createdAt));

    }
    
    // ======== PLATOS =========
    

    // function cargarPlatos
    // Carga en la BD los platos almacenados en el archivo platos.json
    public function cargarPlatos()
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
    

    
    

}
