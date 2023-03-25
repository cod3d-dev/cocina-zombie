<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.parciales.head')
</head>
<body>
    
     <div class="col-lg-8 mx-auto p-3 py-md-5">
          @include('layouts.parciales.header')

          
          <main>

               @yield('contenido')
               
               @include('layouts.parciales.footer')
          </main>
     </div>
     
     @if (session()->has('tipoMensaje'))
          <div class="alert alert-{{ session('tipoMensaje') }} alert-dismissible position-fixed w-100 bottom-0 {{ session('tipoMensaje') }}" role="alert">
               <div>{{ session('mensaje') }}</div>
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
     @endif
    @include('layouts.parciales.footer-scripts')
    
</body>
</html>
