<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use function PHPUnit\Framework\isNull;

class ComandaControlador extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $platos = $this->obtenerListaPlatos();

        return view('comandas.crear', compact('platos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

      

        //Esta abierta la cocina?
        if ($this->cocinaAbierta()) {

            $comandaEspecial = false;
            $platos = [];
            $idPlatos = $request->plato;
            $cant = $request->cant;

            // Si la suma de todas las cantidades es mayor a 0, creo la comanda
            if (array_sum($cant)) {
                // Aumento el correlativo y uso ese nuevo valor como el número de la comanda
                $fecha = time();
                $numComanda = Redis::incr('comandas:correlativo');

                $comanda = Redis::hset('comandas:' . $numComanda, 'mesa', $request->mesa);
                $comanda = Redis::hset('comandas:' . $numComanda, 'createdAt', $fecha);
                $comanda = Redis::hset('comandas:' . $numComanda, 'id', $numComanda);


                // Itero entre el array de cantidades para obtener la información del plato
                $lineas = 0;

                for ($i = 0; $i < count($cant); $i++) {
                    if ($cant[$i] > 0) {
                        $lineas++;

                        $plato = $this->obtenerPlato($idPlatos[$i]);

                        $agregarLinea = Redis::hset('comandas:' . $numComanda . ':linea:' . $lineas, 'id', $idPlatos[$i]);
                        $agregarLinea = Redis::hset('comandas:' . $numComanda . ':linea:' . $lineas, 'cant', $cant[$i]);

                        if ($plato['tipo'] == 'especial') {
                            $comandaEspecial = true;
                        }
                    }
                }


                $comanda = Redis::hset('comandas:' . $numComanda, 'lineas', $lineas);
                $comanda = Redis::hset('comandas:' . $numComanda, 'especial', $comandaEspecial);

                $cola = $this->agregarCola($fecha, $numComanda, $comandaEspecial);
                
                
                return redirect()->back()->with('tipoMensaje', 'success')->with('mensaje', '¡Comanda creada exitosamente!');
            } else {
                return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡Las cantidades deben ser mayores a 0!');
            }
        } else {
            return redirect()->back()->with('tipoMensaje', 'danger')->with('mensaje', '¡Cocina Cerrada!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        $comanda = $this->obtenerComanda($id);
        if(!empty($comanda)){

            return view('comandas.mostrar', compact('comanda'));
        } else {
            return redirect('/')->with('tipoMensaje', 'danger')->with('mensaje', '¡Comanda no encontrada!');
        }

    }

   
    public function ultima()
    {
        $ultimaComanda = Redis::get('comandas:correlativo');
        $comanda = $this->obtenerComanda($ultimaComanda);
        
      

        return view('comandas.mostrar', compact('comanda'));
        
    }

    // Muestra vista con la cola de comandas
    public function cola()
    {
        $cola = json_decode($this->obtenerCola());

        return view('comandas.cola', compact('cola'));
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
    

    // Obtener lista de todos los platos
    public function obtenerListaPlatos()
    {
        $listaPlatos = Redis::smembers('platos:lista');


        for ($i = 0; $i < count($listaPlatos); $i++) {
            $platos[$i] = Redis::hgetall('platos:' . $listaPlatos[$i]);
        }

        return $platos;
    }

    // Obtengo los datos del plato
    public function obtenerPlato($id)
    {
        $plato = Redis::hgetall('platos:' . $id);
        return $plato;
    }

    // ======== COLA =========
    
    // function cocinaAbierta
    // Revisa si la cocina está abierta */
    public function cocinaAbierta() {
        $cocinaAbierta = Redis::get('cocina:abierta');

        // Si no existe el elemento, lo creo e inicializo en true
        if (is_null($cocinaAbierta)) {
            $cocinaAbierta = Redis::incr('cocina:abierta');
        }
        return $cocinaAbierta;
    }

    public function abrirCocina(){
        $abrir = Redis::set('cocina:abierta', 1);
        return $abrir;
    }

    // Agrego comanda al set que lleva la cola
    public function agregarCola($fecha, $idComanda, $especial)
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
            $agregado = Redis::zadd('cola', $posicion, $idComanda);


            // Si falta 1 para llenarse la cola, cierro la cocina
            if ($tamCola == $maxCola -1) {
                $cerrar = Redis::set('cocina:abierta', 0);
            }

            return $agregado;
        } else {
            // Cierro la cocina
            $cerrar = Redis::set('cocina:abierta', 0);
            return 0;
        }
    }

    // Carga de la BD la cola
    public function obtenerCola()
    {

        $cola = [];
        $tamCola = Redis::zCard('cola');
        $comandasCola = Redis::zRangeByScore('cola', 0, $tamCola);



        for ($i = 0; $i < $tamCola; $i++) {
            $comanda = $this->obtenerComanda($comandasCola[$i]);
            // dd($comanda);
            $platos = $this->obtenerPlatosComanda($comanda['id']);
            array_push($cola, $comanda);
            $cola[$i]["platos"] = $platos;
            // dd(json_encode($comanda));
        }

        return json_encode($cola);
    }


    // Elimino el set de la cola para procesar todas las comandas
    public function procesarTodas()
    {

        $procesadas = Redis::del('cola');
        
        if($procesadas){
            $this->abrirCocina();
            return redirect()->back()->with('tipoMensaje', 'success')->with('mensaje', '¡Se procesaron todas las comandas!');
        } else {
            return redirect()->back()->with('tipoMensaje', 'warning')->with('mensaje', '¡No hay comandas en cola!');
        }
    }



    // ======== COMANDAS =========
    
    // function obtenerComanda
    // Devuelvo un array con la comanda */
    public function obtenerComanda($id)
    {
        $datosComanda = $this->obtenerDatosComanda($id);
        
        if(empty($datosComanda)){
            return [];
        }

        $platosComanda = $this->obtenerPlatosComanda($id);

        $comanda = $datosComanda;
        $comanda['platos'] = $platosComanda;

        return $comanda;
    }

    // function obtenerDatosComanda
    // Obtengo los datos de la comanda de la BD */
    public function obtenerDatosComanda($id) {
        
        $comanda = Redis::hgetall('comandas:' . $id);
        return $comanda;

    }

    // function obtenerPlatosComanda
    // Obtengo los platos asociados a la comanda de la BD */
    public function obtenerPlatosComanda($idComanda)
    {
        $platos = [];
        $lineas = Redis::hget('comandas:' . $idComanda, 'lineas');
        
        for($i=1; $i <= $lineas ; $i++) {
            $idPlato = Redis::hget('comandas:' . $idComanda . ':linea:' . $i, 'id');
           
            $cant = Redis::hget('comandas:' . $idComanda . ':linea:' . $i, 'cant');
            $nombrePlato = Redis::hget('platos:' . $idPlato, 'nombre');


            array_push($platos, ["id" => $idPlato, "nombre" => $nombrePlato, "cant" => $cant]);
        } 
        return $platos;
    }
    
}
