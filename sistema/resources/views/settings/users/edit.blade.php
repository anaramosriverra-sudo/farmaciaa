@extends('template.administrador')

@section('title', 'Usuarios')
@section('name-section', 'USUARIOS')
@section('descripcion', 'Editar usuario')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Usuarios')
@section('section-url', route('users.index'))
@section('item-breadcrumb', 'Editar')

@section('content')
<form action="{{ route('users.update', $user) }}" method="POST" autocomplete="off">
  @csrf
  @method('PUT')
  @include('settings.users._form')
</form>
@endsection
