<?php

namespace App\Http\Livewire;
use Illuminate\Support\Facades\Redis;

use Livewire\Component;

class Platos extends Component
{
    
    public $totalPlatos;
    public $platos = [];
    
    public function render()
    {
        return view('livewire.platos');
    }
}
