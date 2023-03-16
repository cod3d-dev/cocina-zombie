@extends('layouts.principal')

@section('contenido')
     
        <div class="card text-left mt-5">
          <div class="card-body">
            <div class="container">
              <div class="row my-5">

                <div class="d-flex justify-content-around">
                  <a href="{{ route('comandas.create') }}" class="btn btn-danger btn-lg p-3">Nueva Comanda</a>
                  <a href="{{ route('comandas/ultima') }}" class="btn btn-warning btn-lg p-3">Última Comanda</a>
                </div>
                
            </div>
          </div>
        </div>

      </form>
@endsection
