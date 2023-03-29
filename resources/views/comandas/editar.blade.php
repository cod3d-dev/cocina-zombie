@extends('layouts.principal')

@inject('carbon', 'Carbon\Carbon')

@section('contenido')

<form action="{{ route('comandas.update') }}" method="post">
     @csrf
     <div class="card h-100">
          <div class="card-header">
               <h5 class="card-title text-center">Información de la comanda</h5>
          </div>

          <div class="card-body">
               <ul class="list-group list-group-flush">
                    <li class="list-group-item">Comanda: {{ $comanda['id'] }} <input type="hidden" name="idComanda"
                              value="{{ $comanda['id'] }}"></li>
                    <li class="list-group-item">Mesa: {{ $comanda['mesa'] }}</li>
                    <li class="list-group-item fw-bolder bg-light">Platos</li>
                    @foreach($comanda['platos'] as $plato)
                    <li class="list-group-item">{{ $plato['nombre'] }}: {{ $plato['cant'] }}</li>
                    @endforeach
               </ul>
          </div>


          <div class="card-footer text-end">
               <small class="text-body-secondary text-end"><input type="datetime-local"
                         value="{{ $carbon::createFromTimestamp($comanda['createdAt'])->isoFormat('LLLL'); }}"
                         name="createdAt"></small>

          </div>

     </div>

     <div class="d-flex justify-content-end mt-3">
          <a href="{{ url('/') }}" class="btn btn-success">Regresar</a>
          <button class="btn btn-danger ms-2" type="submit">Guardar</button>
     </div>
</form>
@endsection