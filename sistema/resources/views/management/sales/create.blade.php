@extends('template.administrador')

@section('title', 'VENTAS')
@section('name-section', 'VENTAS')
@section('descripcion', 'Registrar una nueva venta')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')
@section('item-breadcrumb', 'Nueva')

@section('content')
@php
  $medicineData = $medicines->map(fn ($m) => [
      'id' => $m->id,
      'name' => $m->name,
      'price' => (float) $m->price,
      'stock' => (int) $m->stock,
  ])->values();
@endphp

<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
      @csrf
      @include('partials.form-actions')

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="customer_id" class="form-label">Cliente</label>
          <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
            <option value="">— Seleccione —</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                {{ $customer->name }} — DNI {{ $customer->dni }}
              </option>
            @endforeach
          </select>
          @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="form-text">
            ¿Cliente nuevo? <a href="{{ route('customers.create') }}">Regístralo aquí</a>.
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle" id="itemsTable">
          <thead>
            <tr>
              <th class="text-uppercase small" style="min-width:260px">Medicamento</th>
              <th class="text-end text-uppercase small">Precio</th>
              <th class="text-uppercase small" style="width:120px">Cantidad</th>
              <th class="text-end text-uppercase small">Subtotal</th>
              <th style="width:50px"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addRow">
        <i class="bi bi-plus-lg me-1"></i>Agregar medicamento
      </button>

      <div class="row justify-content-end">
        <div class="col-md-4">
          <div class="d-flex justify-content-between"><span class="text-secondary">Subtotal (sin IGV)</span><span id="tSubtotal">S/ 0.00</span></div>
          <div class="d-flex justify-content-between"><span class="text-secondary">IGV ({{ round(\App\Models\Sale::IGV_RATE * 100) }}%)</span><span id="tIgv">S/ 0.00</span></div>
          <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mt-2"><span>TOTAL</span><span id="tTotal">S/ 0.00</span></div>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary text-white">Registrar venta</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const medicines = @json($medicineData);
  const previous = @json(old('items', []));
  const IGV_RATE = {{ \App\Models\Sale::IGV_RATE }};
  const tbody = document.querySelector('#itemsTable tbody');
  let counter = 0;

  const money = (n) => 'S/ ' + n.toFixed(2);
  const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const findMedicine = (id) => medicines.find((m) => String(m.id) === String(id));

  function recalc() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach((tr) => {
      const med = findMedicine(tr.querySelector('.item-medicine').value);
      const qty = parseInt(tr.querySelector('.item-amount').value, 10) || 0;
      const price = med ? med.price : 0;
      const sub = Math.round(price * qty * 100) / 100;
      tr.querySelector('.item-price').textContent = money(price);
      tr.querySelector('.item-subtotal').textContent = money(sub);
      total += sub;
    });
    const igv = total - total / (1 + IGV_RATE);
    document.getElementById('tTotal').textContent = money(total);
    document.getElementById('tIgv').textContent = money(igv);
    document.getElementById('tSubtotal').textContent = money(total - igv);
  }

  function addRow(medicineId, amount) {
    const i = counter++;
    const tr = document.createElement('tr');
    const options = medicines
      .map((m) => '<option value="' + m.id + '">' + escapeHtml(m.name) + ' (stock ' + m.stock + ')</option>')
      .join('');

    tr.innerHTML =
      '<td><select name="items[' + i + '][medicine_id]" class="form-select form-select-sm item-medicine" required>' +
        '<option value="">— Seleccione —</option>' + options + '</select></td>' +
      '<td class="text-end item-price">S/ 0.00</td>' +
      '<td><input type="number" name="items[' + i + '][amount]" class="form-control form-control-sm item-amount" min="1" step="1" value="' + (amount || 1) + '" required></td>' +
      '<td class="text-end item-subtotal">S/ 0.00</td>' +
      '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger item-remove" title="Quitar"><i class="bi bi-x-lg"></i></button></td>';

    tbody.appendChild(tr);

    const select = tr.querySelector('.item-medicine');
    const qty = tr.querySelector('.item-amount');
    if (medicineId) select.value = medicineId;

    const syncMax = () => {
      const med = findMedicine(select.value);
      if (med) {
        qty.max = med.stock;
        if (parseInt(qty.value, 10) > med.stock) qty.value = med.stock;
      } else {
        qty.removeAttribute('max');
      }
    };

    select.addEventListener('change', () => {
      const duplicated = Array.from(tbody.querySelectorAll('.item-medicine'))
        .some((other) => other !== select && select.value !== '' && other.value === select.value);
      if (duplicated) {
        alert('Ese medicamento ya está en la lista. Modifica su cantidad.');
        select.value = '';
      }
      syncMax();
      recalc();
    });
    qty.addEventListener('input', () => { syncMax(); recalc(); });
    tr.querySelector('.item-remove').addEventListener('click', () => { tr.remove(); recalc(); });

    syncMax();
    recalc();
  }

  document.getElementById('addRow').addEventListener('click', () => addRow('', 1));

  document.getElementById('saleForm').addEventListener('submit', (e) => {
    if (tbody.querySelectorAll('tr').length === 0) {
      e.preventDefault();
      alert('Agrega al menos un medicamento a la venta.');
    }
  });

  const rows = Object.values(previous || {});
  if (rows.length) {
    rows.forEach((row) => addRow(row.medicine_id, row.amount));
  } else {
    addRow('', 1);
  }
})();
</script>
@endpush
