@extends('template.administrador')

@section('title', 'Usuarios')
@section('name-section', 'USUARIOS')
@section('descripcion', 'Nuevo registro de usuario')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Usuarios')
@section('section-url', route('users.index'))
@section('item-breadcrumb', 'Nuevo')

@section('content')
<form action="{{ route('users.store') }}" method="POST" autocomplete="off">
  @csrf
  
  @include('settings.users._form')
</form>
@endsection
