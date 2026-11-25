@extends('template.administrador')

@section('title', 'USUARIOS')
@section('name-section', 'USUARIOS')
@section('descripcion', 'Nuevo registro de usuario')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Usuarios')
@section('item-breadcrumb', 'Nuevo')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
      @csrf
      @include('settings.users._form')
    </form>
  </div>
</div>
@endsection
