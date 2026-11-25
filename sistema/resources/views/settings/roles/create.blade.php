@extends('template.administrador')

@section('title', 'Roles')
@section('name-section', 'ROLES')
@section('descripcion', 'Nuevo registro de rol')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Roles')
@section('section-url', route('roles.index'))
@section('item-breadcrumb', 'Nuevo')

@section('content')
<form action="{{ route('roles.store') }}" method="POST">
  @csrf
  
  @include('settings.roles._form')
</form>
@endsection
