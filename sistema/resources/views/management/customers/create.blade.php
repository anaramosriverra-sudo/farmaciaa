@extends('template.administrador')

@section('title', 'CLIENTES')
@section('name-section', 'CLIENTES')
@section('descripcion', 'Nuevo registro de cliente')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Clientes')
@section('item-breadcrumb', 'Nuevo')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <form action="{{ route('customers.store') }}" method="POST">
      @csrf
      @include('management.customers._form')
    </form>
  </div>
</div>
@endsection
