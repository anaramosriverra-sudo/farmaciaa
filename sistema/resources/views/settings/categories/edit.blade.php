@extends('template.administrador')

<<<<<<< Updated upstream
@section('title', 'CATEGORIAS')
@section('name-section', 'CATEGORIAS')

@section('description', 'Actualizar registro de categoria')
@section('item-breadcrumb', 'Actualizar')
@section('section-breadcrumb', 'Categorías')
@section('module-breadcrumb', 'Configuraciones')


@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
                <div class="card-body">
                    <form action="{{route('categories.update', $category->id)}}" method="POST">
                    
                    @csrf
                    @method('put')
                    @include('settings.categories._form')
                    </form>
                </div>
            </div>


@endsection
=======
@section('title', 'Categorías')
@section('name-section', 'CATEGORÍAS')
@section('descripcion', 'Editar categoría')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Categorías')
@section('section-url', route('categories.index'))
@section('item-breadcrumb', 'Editar')

@section('content')
<form action="{{ route('categories.update', $category) }}" method="POST">
  @csrf
  @method('PUT')
  @include('settings.categories._form')
</form>
@endsection
>>>>>>> Stashed changes
