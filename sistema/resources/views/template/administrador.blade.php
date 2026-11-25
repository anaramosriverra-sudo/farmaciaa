<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< Updated upstream
=======
  <meta name="csrf-token" content="{{ csrf_token() }}">
>>>>>>> Stashed changes
  <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
  <title>FARM SJB | @yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<<<<<<< Updated upstream
=======
  @stack('styles')
>>>>>>> Stashed changes
</head>

<body>

<div id="app-wrapper" class="d-flex">

<<<<<<< Updated upstream
 <!-- sidebar -->
  @include('partials.sidebar')

  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
 <!-- header -->
  @include('partials.header')

=======
  <!-- sidebar -->
  @include('partials.sidebar')

  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
    <!-- header -->
    @include('partials.header')
>>>>>>> Stashed changes

    <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row gap-2 mb-4">

<<<<<<< Updated upstream
        <p class="text-secondary mb-0">@yield('descripcion')</p>

          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="Ruta de navegación">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item">
                  <span>@yield('item-breadrumb')</span>
              </li>
               <li class="breadcrumb-item text-muted fw-medium" aria-current="page">
                <a href="" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                  @yield('item-breadrumb')
                </a>
              </li>
              <li class="breadcrumb-item">
                <span class="text-secondary">@yield('item-breadrumb')</span>
              </li>
=======
          <p class="text-secondary mb-0">@yield('descripcion')</p>

          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="Ruta de navegación">
            <ol class="breadcrumb mb-0 small">
              @hasSection('module-breadcrumb')
                <li class="breadcrumb-item"><span>@yield('module-breadcrumb')</span></li>
              @endif
              @hasSection('section-breadcrumb')
                <li class="breadcrumb-item text-muted fw-medium">
                  @hasSection('section-url')
                    <a href="@yield('section-url')" class="text-decoration-none text-secondary">@yield('section-breadcrumb')</a>
                  @else
                    <span class="text-secondary">@yield('section-breadcrumb')</span>
                  @endif
                </li>
              @endif
              @hasSection('item-breadcrumb')
                <li class="breadcrumb-item" aria-current="page"><span class="text-secondary">@yield('item-breadcrumb')</span></li>
              @endif
>>>>>>> Stashed changes
            </ol>
          </nav>
        </div>

<<<<<<< Updated upstream

        <!-- 
            CONTENIDO DE LA VISTA
            Aquí va el contenido específico de cada página
            (KPIs, gráficos, tablas, formularios, etc.)
            En Laravel: 
        -->

        @yield('content')

=======
        @include('partials.alerts')

        @yield('content')
>>>>>>> Stashed changes
      </div>
    </main>

  </div>
</div>

<!-- MODAL — Confirmar Cierre de Sesión -->
<<<<<<< Updated upstream
  @include('partials.modal-logout')

=======
@include('partials.modal-logout')
<!-- MODAL — Confirmar eliminación -->
@include('partials.modal-delete')
>>>>>>> Stashed changes

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<<<<<<< Updated upstream

=======
@stack('scripts')
>>>>>>> Stashed changes

</body>
</html>
