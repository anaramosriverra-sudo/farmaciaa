@extends('template.administrador')

@section('title','CATEGORIAS')
@section('name-section','CATEGORIAS')

@section('descripcion','Nuevo registro de categoria')
@section('title-breadcrumb','nuevo')
@section('section-breadcrumb','categorias')
@section('module-breadcrumb','Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
                
                <div class="card-body">
                   <form action="{{route('categories.store') }}" method="POST">
                    @csrf 
                    @include('settings.categories._form')
                </form>
                </div>
            </div>


@endsection