@extends('layouts.principal')
@php
// dd($comanda);  
@endphp
@inject('carbon', 'Carbon\Carbon')

@section('contenido')


<div class="row row-cols-1 row-cols-md-3 g-4">

     @foreach($cola as $i => $comanda)
     <div class="col">
       <div class="card h-100">
          <div class="card-header">
           <h5 class="card-title text-center">{{ $i+1 }}</h5>
         </div>
         
         <div class="card-body">
          <ul class="list-group list-group-flush">
               <li class="list-group-item">Comanda: {{ $comanda->id }}</li>
               <li class="list-group-item">Mesa: {{ $comanda->mesa }}</li>
               <li class="list-group-item fw-bolder bg-light">Platos</li>
               @foreach($comanda->platos as $plato)
                    <li class="list-group-item">{{ $plato->nombre }}: {{ $plato->cant }}</li>
               @endforeach
             </ul>
         </div>
         
      
         <div class="card-footer text-end">
           <small class="text-body-secondary text-end">{{ $carbon::createFromTimestamp($comanda->createdAt)->isoFormat('LLLL'); }}</small>
           
         </div>
       </div>
     </div>
    
     @endforeach
 

   </div>
   <div class="d-flex justify-content-end mt-4">
     <a href="{{ url('/') }}" class="btn btn-success me-3">Regresar</a>
     <a href="{{ route('cola/procesar/todas') }}" class="btn btn-danger me-3">Procesar</a>
   </div>



@endsection
