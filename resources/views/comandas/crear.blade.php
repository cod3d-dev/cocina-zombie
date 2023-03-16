@extends('layouts.principal')

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
                    <input class="form-control" type="text" placeholder="Nombre del cliente..." name="cliente" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <label for="fecha" class="input-group-text">Fecha:</label>
                    <span class="form-control">{{ now()->format('d-m-Y h:i:s') }}</span>
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
                  <a href="{{ url()->previous() }}" class="btn btn-success me-3">Regresar</a>
                  <button class="btn btn-danger">Procesar</button>
                </div>
            </div>
          </div>
        </div>

      </form>
@endsection