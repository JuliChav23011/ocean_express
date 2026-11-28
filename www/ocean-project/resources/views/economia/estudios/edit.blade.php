
@extends('layouts.app')

@section('title', 'Editar estudio de inversión')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h1 class="fw-bold">Editar estudio de inversión</h1>

            <p class="text-muted mb-0">
                Modificación de los datos del estudio {{ $estudio->codigo }}.
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
                <i class="bi bi-pencil-square"></i>
                Actualizar datos del estudio
            </h5>

        </div>

        <div class="card-body p-4">

            <form action="{{ route('economia.estudios.update', $estudio) }}"
                  method="POST">

                @csrf
                @method('PUT')

                @include('economia.estudios.form')

            </form>

        </div>

    </div>

</div>

@endsection
