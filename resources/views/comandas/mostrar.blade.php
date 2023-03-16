@extends('layouts.principal')
@php
// dd($comanda);  
@endphp

@section('contenido')
      <form action="{{ route('comandas.store') }}" method="post">
        @csrf
        <div class="card text-left mt-5">
          <div class="card-body">
            <h4 class="card-title"><h2>Nueva comanda</h2></h4>
            <div class="container mt-5">
              <div class="row">

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <label for="fecha" class="input-group-text">Cliente:</label>
                    <span class="form-control">{{ $comanda['cliente'] }}</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <label for="fecha" class="input-group-text">Fecha:</label>
                    <span class="form-control">{{  date("d/m/Y h:i:s", $comanda['fecha']) }}</span>
                  </div>
                </div>
                <hr class="my-3">

                <div class="row">

                  <h3>Pedido</h3>
                  <div class="mt-3">
                    ¡No tenemos ningún plato en este momento! 
                  </div>
                
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-end">
                  <a href="{{ url()->previous() }}" class="btn btn-success">Regresar</a>
                </div>
            </div>
          </div>
        </div>

      </form>
@endsection
