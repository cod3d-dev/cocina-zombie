<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\ComandaControlador;

use Cript;
use Cmda;
use Cola;

class ControladorCola extends Component
{
    public $cola = [];
    public $despachadas = [];

    public function mount() {

        
        $this->cola = Cola::obtener();
        $this->despachadas = Cola::despachadas();
        
    }
    
    public function render()
    {
        
        return view('livewire.controlador-cola');
    }

    public function procesarCola() {
        $procesar = Cola::procesar();
        $this->mount();
    }

    public function revisarHackeo() {
        foreach($this->cola as $i => $comanda) {
            if($comanda['createdAt']<>Cript::decriptar($comanda['createdAtCrip'])) {
                $this->cola[$i]['hackeada'] = "true";
            }
            
        }
        $this->render();
    }

    public function corregirHackeo() {
        foreach($this->cola as $i => $comanda) {
            if($comanda['createdAt']<>Cript::decriptar($comanda['createdAtCrip'])) {
                $corregir = Cmda::modificar($comanda['id'], 'createdAt', Cript::decriptar($comanda['createdAtCrip']));
            }
        }
        $this->mount();
    }
}
