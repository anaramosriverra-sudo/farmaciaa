<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
  <title>FARM SJB | @yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  @stack('styles')
</head>

<body>

<div id="app-wrapper" class="d-flex">

  @include('partials.sidebar')

  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
    @include('partials.header')

    <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row gap-2 mb-4">

          <p class="text-secondary mb-0">@yield('descripcion')</p>

          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="Ruta de navegación">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-secondary">Inicio</a>
              </li>
              @hasSection('module-breadcrumb')
                <li class="breadcrumb-item"><span class="text-secondary">@yield('module-breadcrumb')</span></li>
              @endif
              @hasSection('section-breadcrumb')
                <li class="breadcrumb-item"><span class="text-secondary">@yield('section-breadcrumb')</span></li>
              @endif
              @hasSection('item-breadcrumb')
                <li class="breadcrumb-item text-muted fw-medium" aria-current="page">@yield('item-breadcrumb')</li>
              @endif
            </ol>
          </nav>
        </div>

        @if(session('ok'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        @endif

        @yield('content')

      </div>
    </main>

  </div>
</div>

@include('partials.modal-logout')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

</body>
</html>
