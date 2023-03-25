@extends('layouts.principal')

@section('contenido')
<form action="{{ route('comandas.store') }}" method="post">
  @csrf
  <div class="card text-left mt-5">
    <div class="card-body">
      <h4 class="card-title">
        <h2>Nueva comanda</h2>
      </h4>
      <div class="container mt-5">
        <div class="row">

          <div class="col-md-6">
            <div class="input-group mb-3">
              <label for="fecha" class="input-group-text">Mesa:</label>
              <select class="form-control" name="mesa" required>
                <option value="">Seleccione la mesa</option>
                <option value="1">Mesa 1</option>
                <option value="2">Mesa 2</option>
                <option value="3">Mesa 3</option>
                <option value="4">Mesa 4</option>
                <option value="5">Mesa 5</option>
              </select>
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

          <hr class="mt-5 mb-4">

          <div class="row">

            <h3>Platos</h3>
            <div class="mt-3">
              <div class="row g-2">
                
                @foreach ($platos as $plato)
                  
                
                <div class="col-4">
                  <div class="p-3 plato border border-2 rounded">
                    {{ $plato['nombre'] }} @if($plato['tipo']=='especial')<span class="badge rounded-pill text-bg-danger">Especial</span>@endif

                    <div class="d-block mt-3">
                      <input class="form-control" type="hidden" name="plato[]" value={{ $plato['id'] }}>
                      <input class="form-control" type="number" name="cant[]" min="0">
                    </div>
                   
                    
                  </div>
                </div>

                @endforeach

              </div>

            </div>

          </div>

          <hr class="my-3">

          <div class="d-flex justify-content-end">
            <a href="{{ url('/') }}" class="btn btn-success me-3">Regresar</a>
            <button class="btn btn-danger">Procesar</button>
          </div>
        </div>
      </div>
    </div>

</form>
@endsection