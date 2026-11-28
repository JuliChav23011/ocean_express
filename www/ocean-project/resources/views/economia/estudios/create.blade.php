
@extends('layouts.app')

@section('title', 'Nuevo estudio de inversión')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h1 class="fw-bold">Nuevo estudio de inversión</h1>

            <p class="text-muted mb-0">
                Registro de un nuevo estudio de Ingeniería Económica.
            </p>
        </div>

        <a href="{{ route('economia.estudios.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0">
                <i class="bi bi-clipboard-plus"></i>
                Datos del estudio de inversión
            </h5>

        </div>

        <div class="card-body p-4">

            <form action="{{ route('economia.estudios.store') }}"
                  method="POST">

                @csrf

                @include('economia.estudios.form')

            </form>

        </div>

    </div>

</div>

@endsection
