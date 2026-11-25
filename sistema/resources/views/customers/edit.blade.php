@extends('template.administrador')

@section('title', 'Clientes')
@section('name-section', 'CLIENTES')
@section('descripcion', 'Editar cliente')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Clientes')
@section('section-url', route('customers.index'))
@section('item-breadcrumb', 'Editar')

@section('content')
<form action="{{ route('customers.update', $customer) }}" method="POST">
  @csrf
  @method('PUT')
  @include('customers._form')
</form>
@endsection
