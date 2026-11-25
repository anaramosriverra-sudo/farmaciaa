@extends('template.administrador')

@section('title', 'ROLES')
@section('name-section', 'ROLES')
@section('descripcion', 'Nuevo registro de rol')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Roles')
@section('item-breadcrumb', 'Nuevo')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <form action="{{ route('roles.store') }}" method="POST">
      @csrf
      @include('settings.roles._form')
    </form>
  </div>
</div>
@endsection
