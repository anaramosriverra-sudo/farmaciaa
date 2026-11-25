@extends('template.administrador')

@section('title', 'Medicamentos')
@section('name-section', 'MEDICAMENTOS')
@section('descripcion', 'Editar medicamento')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Medicamentos')
@section('section-url', route('medicamentos.index'))
@section('item-breadcrumb', 'Editar')

@section('content')
<form action="{{ route('medicamentos.update', $medicamento) }}" method="POST">
  @csrf
  @method('PUT')
  @include('medicamentos._form')
</form>
@endsection
