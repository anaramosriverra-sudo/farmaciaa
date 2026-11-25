@extends('template.administrador')

@section('title', 'MEDICAMENTOS')
@section('name-section', 'MEDICAMENTOS')
@section('descripcion', 'Nuevo registro de medicamento')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Medicamentos')
@section('item-breadcrumb', 'Nuevo')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <form action="{{ route('medicines.store') }}" method="POST">
      @csrf
      @include('management.medicines._form')
    </form>
  </div>
</div>
@endsection
