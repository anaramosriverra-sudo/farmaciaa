@extends('template.administrador')

@section('title', 'Clientes')
@section('name-section', 'CLIENTES')
@section('descripcion', 'Nuevo registro de cliente')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Clientes')
@section('section-url', route('customers.index'))
@section('item-breadcrumb', 'Nuevo')

@section('content')
<form action="{{ route('customers.store') }}" method="POST">
  @csrf
  
  @include('customers._form')
</form>
@endsection
