   <header class="app-header bg-white border-bottom shadow-sm d-flex align-items-center px-3 px-lg-4 gap-3">

      <button class="btn btn-sm btn-outline-secondary border-0 sidebar-toggle-btn"
              data-sidebar-toggle
              aria-label="Alternar menú lateral">
        <i class="bi bi-list fs-5"></i>
      </button>

      <h1 class="h5 fw-bold mb-0 me-auto">@yield('name-section')</h1>

      <div class="d-flex align-items-center gap-2">

        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary border-0 position-relative rounded-3"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
            @if($lowStockCount > 0)
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                    style="font-size:.58rem;padding:.28em .45em">{{ $lowStockCount }}</span>
            @endif
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border" style="min-width:290px">
            <li><h6 class="dropdown-header">Notificaciones</h6></li>
            @forelse($lowStockAlerts as $alert)
              <li>
                <a class="dropdown-item py-2" href="{{ route('medicines.edit', $alert) }}">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle text-danger mt-1 flex-shrink-0"></i>
                    <div>
                      <div class="small fw-medium">Stock bajo: {{ $alert->name }}</div>
                      <div class="text-secondary" style="font-size:.72rem">
                        {{ $alert->stock === 0 ? 'Sin stock' : 'Quedan '.$alert->stock.' unidades' }}
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            @empty
              <li>
                <div class="dropdown-item-text small text-secondary py-2">
                  <i class="bi bi-check-circle text-success me-1"></i>Sin alertas de stock.
                </div>
              </li>
            @endforelse
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-center small text-primary fw-medium py-2" href="{{ route('reports.index') }}">
                Ver reporte de stock
              </a>
            </li>
          </ul>
        </div>

      </div>
    </header>