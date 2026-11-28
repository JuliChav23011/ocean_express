@extends('layouts.app')

@section('title', 'Editar alternativa de inversión')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                <i class="bi bi-pencil-square"></i>
                Editar alternativa de inversión
            </h2>

            <p class="text-muted mb-0">
                Modifique la información económica y financiera de la alternativa.
            </p>

        </div>

        <a href="{{ route('economia.alternativas.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                Editando: {{ $alternativa->nombre }}
            </h5>

        </div>

        <div class="card-body p-4">

            <form action="{{ route('economia.alternativas.update', $alternativa) }}"
                  method="POST">

                @csrf
                @method('PUT')

                @include('economia.alternativas.form')

            </form>

        </div>

    </div>

</div>

@endsection