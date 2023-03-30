@inject('carbon', 'Carbon\Carbon')
<div>
    <div class="row">
        <div class="col-11">

    
            <div class="row row-cols-1 row-cols-md-5 g-4">
                @foreach($cola as $i => $comanda)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title text-center">{{ $comanda['id'] }}<br><span
                                    class="badge rounded-pill {{ $comanda['especial'] ? 'text-bg-danger' : 'text-bg-success'}}">{{
                                    $comanda['especial'] ? 'Especial' : 'Normal'}}</span></h5>
                        </div>
    
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Mesa: {{ $comanda['mesa'] }}</li>
                                <li class="list-group-item fw-bolder bg-light">Platos</li>
                                @foreach($comanda['platos'] as $plato)
                                <li class="list-group-item">{{ $plato['nombre'] }}: {{ $plato['cant'] }}</li>
                                @endforeach
                            </ul>
                        </div>
    
    
                        <div class="card-footer text-end">
                            <small class="text-body-secondary text-end">{{
                                $carbon::createFromTimestamp($comanda['createdAt'])->isoFormat('LLLL');
                                }}</small>@if(array_key_exists('hackeada', $comanda))<span
                                class="badge rounded-pill text-bg-danger">Hackeada</span>@endif
    
                        </div>
                    </div>
                </div>
    
                @endforeach
    
    
            </div>

            <h2 class="mt-3">Despachadas</h2>
            <div class="row row-cols-1 row-cols-md-5 g-4 mt-1">
                @foreach($despachadas as $i => $comandaD)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title text-center">{{ $comandaD['id'] }}<br><span
                                    class="badge rounded-pill {{ $comandaD['especial'] ? 'text-bg-danger' : 'text-bg-success'}}">{{
                                    $comandaD['especial'] ? 'Especial' : 'Normal'}}</span></h5>
                        </div>
    
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Fecha: {{ $carbon::createFromTimestamp($comandaD['createdAt'])->isoFormat('D/M/Y H:mm'); }}</li>
                                <li class="list-group-item">Mesa: {{ $comandaD['mesa'] }}</li>
                                <li class="list-group-item fw-bolder bg-light">Platos</li>
                                @foreach($comandaD['platos'] as $plato)
                                <li class="list-group-item">{{ $plato['nombre'] }}: {{ $plato['cant'] }}</li>
                                @endforeach
                            </ul>
                        </div>
    
                        <div class="card-footer text-end">
                            <small class="text-body-secondary text-end">Entrega: {{
                                $carbon::createFromTimestamp($comandaD['dispatchedAt'])->isoFormat('D/M/Y H:mm');
                                }}</small>@if(array_key_exists('hackeada', $comandaD))<span
                                class="badge rounded-pill text-bg-danger">Hackeada</span>@endif
    
                        </div>
                    </div>
                </div>
    
                @endforeach
    
    
            </div>
    
        </div>
    
        <div class="col-1">
            <div class="justify-content-around align-content-around">
                <a href="#" wire:click="revisarHackeo" class="btn btn-warning mt-3 w-100">Revisar</a>
                <a href="#" wire:click="corregirHackeo" class="btn btn-success mt-3 w-100">Corregir</a>
                <a href="#" wire:click="procesarCola" class="btn btn-info mt-3 w-100">Procesar</a>
                <a href="#" wire:click="procesarTodas" class="btn btn-info mt-3 w-100">Procesar Todas</a>
                <a href="{{ url('/') }}" class="btn btn-success w-100 mt-3">Regresar</a>
                
                
                
            </div>
        </div>
    </div>
    
    
</div>