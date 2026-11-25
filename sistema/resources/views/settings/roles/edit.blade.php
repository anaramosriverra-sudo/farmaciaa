@extends('template.administrador')

@section('title', 'Roles')
@section('name-section', 'ROLES')
@section('descripcion', 'Editar rol')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Roles')
@section('section-url', route('roles.index'))
@section('item-breadcrumb', 'Editar')

@section('content')
<form action="{{ route('roles.update', $role) }}" method="POST">
  @csrf
  @method('PUT')
  @include('settings.roles._form')
</form>
@endsection
