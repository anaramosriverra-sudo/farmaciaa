<header class="app-header bg-white border-bottom shadow-sm d-flex align-items-center px-3 px-lg-4 gap-3">
    <button class="btn btn-sm btn-outline-secondary border-0 sidebar-toggle-btn"
            data-sidebar-toggle
            aria-label="Alternar menú lateral">
    <i class="bi bi-list fs-5"></i>
    </button>

    <h1 class="h5 fw-bold mb-0 me-auto">@yield('name_section')</h1>

    <div class="d-flex align-items-center gap-2">

        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary border-0 position-relative rounded-3"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
            @if ($lowStockCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                    style="font-size:.58rem;padding:.28em .45em">{{ $lowStockCount }}</span>
            @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border" style="min-width:290px">
            <li><h6 class="dropdown-header">Notificaciones</h6></li>
            {{-- Alertas reales: medicamentos con stock bajo (AppServiceProvider) --}}
            @forelse ($lowStockAlerts as $medicine)
            <li>
                <div class="dropdown-item py-2">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle text-danger mt-1 flex-shrink-0"></i>
                    <div>
                    <div class="small fw-medium">Stock bajo: {{ $medicine->name }}</div>
                    <div class="text-secondary" style="font-size:.72rem">Quedan {{ $medicine->stock }} unidades</div>
                    </div>
                </div>
                </div>
            </li>
            @empty
            <li><span class="dropdown-item-text small text-secondary">No hay alertas de stock.</span></li>
            @endforelse
            @if (auth()->user()->hasRole('Administrador', 'Químico farmacéutico'))
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-center small text-primary fw-medium py-2" href="{{ route('medicines.index') }}">
                Ver medicamentos
                </a>
            </li>
            @endif
            </ul>
        </div>
    </div>
</header>