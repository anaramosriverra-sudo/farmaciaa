@extends('template.administrador')

@section('title', 'Medicamentos')
@section('name-section', 'MEDICAMENTOS')
@section('descripcion', 'Nuevo registro de medicamento')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Medicamentos')
@section('section-url', route('medicamentos.index'))
@section('item-breadcrumb', 'Nuevo')

@section('content')
<form action="{{ route('medicamentos.store') }}" method="POST">
  @csrf
  
  @include('medicamentos._form')
</form>
@endsection
