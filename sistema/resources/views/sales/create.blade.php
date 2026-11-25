@extends('template.administrador')

@section('title', 'Nueva venta')
@section('name-section', 'VENTAS')
@section('descripcion', 'Registrar una nueva venta')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')
@section('section-url', route('sales.index'))
@section('item-breadcrumb', 'Nueva')

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
  @csrf
  <div class="row g-3">
    <div class="col-12 col-xl-8">
      <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
        <div class="card-body">
          <div class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-7">
              <label for="medSelect" class="form-label">Medicamento</label>
              <select id="medSelect" class="form-select">
                <option value="">Seleccione un medicamento</option>
                @foreach ($medicamentos as $m)
                  <option value="{{ $m['id'] }}">{{ $m['name'] }} — S/ {{ number_format($m['price'], 2) }} (stock {{ $m['stock'] }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-6 col-md-2">
              <label for="medQty" class="form-label">Cantidad</label>
              <input type="number" id="medQty" class="form-control" min="1" value="1">
            </div>
            <div class="col-6 col-md-3">
              <button type="button" id="addItem" class="btn btn-primary text-white w-100"><i class="bi bi-plus-lg me-1"></i>Agregar</button>
            </div>
          </div>

          @error('items') <div class="alert alert-danger small py-2">{{ $message }}</div> @enderror
          @foreach ($errors->get('items.*') as $msgs)
            @foreach ($msgs as $msg) <div class="alert alert-danger small py-2">{{ $msg }}</div> @endforeach
          @endforeach
          <div id="cartAlert" class="alert alert-warning small py-2 d-none"></div>

          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr class="small text-uppercase">
                  <th>Medicamento</th>
                  <th class="text-end">P. unit.</th>
                  <th class="text-center" style="width:110px">Cant.</th>
                  <th class="text-end">Subtotal</th>
                  <th style="width:50px"></th>
                </tr>
              </thead>
              <tbody id="cartBody">
                <tr id="cartEmpty"><td colspan="5" class="text-center text-secondary py-4">Aún no agregaste medicamentos.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
        <div class="card-body">
          <div class="mb-3">
            <label for="customer_id" class="form-label">Cliente</label>
            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
              <option value="">Cliente general</option>
              @foreach ($customers as $c)
                <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }} — {{ $c->dni }}</option>
              @endforeach
            </select>
            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <hr>
          <div class="d-flex justify-content-between small text-secondary"><span>Subtotal (sin IGV)</span><span id="tSub">S/ 0.00</span></div>
          <div class="d-flex justify-content-between small text-secondary"><span>IGV (18%)</span><span id="tIgv">S/ 0.00</span></div>
          <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span id="tTotal">S/ 0.00</span></div>

          <button type="submit" class="btn btn-primary text-white w-100 mt-4" id="saveSale" disabled>
            <i class="bi bi-check2-circle me-1"></i>Registrar venta
          </button>
          <a href="{{ route('sales.index') }}" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
  const catalogo = @json($medicamentos);
  const inicial = @json(collect(old('items', []))->map(fn ($i) => ['id' => (int) ($i['medicine_id'] ?? 0), 'qty' => (int) ($i['amount'] ?? 1)])->values());
  const IGV = 0.18;
  const porId = Object.fromEntries(catalogo.map(m => [m.id, m]));
  const carrito = new Map(); // id -> cantidad

  const body = document.getElementById('cartBody');
  const vacio = document.getElementById('cartEmpty');
  const alerta = document.getElementById('cartAlert');
  const money = n => 'S/ ' + n.toFixed(2);
  const esc = t => String(t ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  function aviso(msg) {
    alerta.textContent = msg || '';
    alerta.classList.toggle('d-none', !msg);
  }

  function pintar() {
    body.querySelectorAll('tr.item').forEach(tr => tr.remove());
    let total = 0, i = 0;

    carrito.forEach((qty, id) => {
      const m = porId[id];
      const sub = Math.round(m.price * qty * 100) / 100;
      total += sub;

      const tr = document.createElement('tr');
      tr.className = 'item';
      tr.innerHTML = `
        <td>${esc(m.name)}<div class="text-secondary" style="font-size:.72rem">${esc(m.category)} · stock ${m.stock}</div>
          <input type="hidden" name="items[${i}][medicine_id]" value="${id}">
          <input type="hidden" name="items[${i}][amount]" value="${qty}"></td>
        <td class="text-end">${money(m.price)}</td>
        <td><input type="number" class="form-control form-control-sm text-center qty" min="1" max="${m.stock}" value="${qty}" data-id="${id}"></td>
        <td class="text-end">${money(sub)}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger rm" data-id="${id}" title="Quitar"><i class="bi bi-trash"></i></button></td>`;
      body.appendChild(tr);
      i++;
    });

    vacio.classList.toggle('d-none', carrito.size > 0);
    total = Math.round(total * 100) / 100;
    const igv = Math.round((total - total / (1 + IGV)) * 100) / 100;
    document.getElementById('tTotal').textContent = money(total);
    document.getElementById('tIgv').textContent = money(igv);
    document.getElementById('tSub').textContent = money(total - igv);
    document.getElementById('saveSale').disabled = carrito.size === 0;
  }

  function agregar(id, qty) {
    const m = porId[id];
    if (!m) return;
    const nueva = (carrito.get(id) || 0) + qty;
    if (nueva > m.stock) {
      aviso(`Stock insuficiente de ${m.name}: disponible ${m.stock}.`);
      carrito.set(id, Math.min(m.stock, nueva));
    } else {
      aviso('');
      carrito.set(id, nueva);
    }
  }

  document.getElementById('addItem').addEventListener('click', () => {
    const sel = document.getElementById('medSelect');
    const qty = parseInt(document.getElementById('medQty').value, 10);
    if (!sel.value) return aviso('Selecciona un medicamento.');
    if (!qty || qty < 1) return aviso('La cantidad debe ser al menos 1.');
    agregar(parseInt(sel.value, 10), qty);
    sel.value = '';
    document.getElementById('medQty').value = 1;
    pintar();
  });

  body.addEventListener('change', e => {
    if (!e.target.classList.contains('qty')) return;
    const id = parseInt(e.target.dataset.id, 10);
    const m = porId[id];
    let qty = parseInt(e.target.value, 10);
    if (!qty || qty < 1) qty = 1;
    if (qty > m.stock) { aviso(`Stock insuficiente de ${m.name}: disponible ${m.stock}.`); qty = m.stock; } else { aviso(''); }
    carrito.set(id, qty);
    pintar();
  });

  body.addEventListener('click', e => {
    const btn = e.target.closest('.rm');
    if (!btn) return;
    carrito.delete(parseInt(btn.dataset.id, 10));
    aviso('');
    pintar();
  });

  inicial.forEach(it => { if (porId[it.id]) carrito.set(it.id, Math.min(it.qty || 1, porId[it.id].stock)); });
  pintar();
})();
</script>
@endpush
