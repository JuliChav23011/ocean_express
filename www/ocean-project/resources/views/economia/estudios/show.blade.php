
@extends('layouts.app')

@section('title', 'Detalle del estudio de inversión')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h1 class="fw-bold">Detalle del estudio</h1>

            <p class="text-muted mb-0">
                {{ $estudio->codigo }} - {{ $estudio->nombre }}
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('economia.estudios.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>

            <a href="{{ route('economia.estudios.edit', $estudio) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil-square"></i>
                Editar
            </a>

        </div>

    </div>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle"></i>
                Información general
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">
                    <strong>Empresa</strong>
                    <p>{{ $estudio->empresa->nombre ?? 'Sin empresa' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Código</strong>
                    <p>{{ $estudio->codigo }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Nombre del estudio</strong>
                    <p>{{ $estudio->nombre }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha del estudio</strong>
                    <p>{{ $estudio->fecha_estudio->format('d/m/Y') }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Estado</strong>

                    <p>
                        @switch($estudio->estado)

                            @case('borrador')
                                <span class="badge bg-secondary">
                                    Borrador
                                </span>
                                @break

                            @case('en_evaluacion')
                                <span class="badge bg-warning text-dark">
                                    En evaluación
                                </span>
                                @break

                            @case('finalizado')
                                <span class="badge bg-success">
                                    Finalizado
                                </span>
                                @break

                        @endswitch
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Moneda</strong>
                    <p>{{ $estudio->moneda }}</p>
                </div>

                <div class="col-12">
                    <strong>Descripción</strong>
                    <p>{{ $estudio->descripcion ?: 'Sin descripción' }}</p>
                </div>

                <div class="col-12">
                    <strong>Objetivo</strong>
                    <p>{{ $estudio->objetivo ?: 'Sin objetivo registrado' }}</p>
                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-calculator"></i>
                Parámetros económicos
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-3 text-center">

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Horizonte</small>

                        <h3 class="text-primary mt-2">
                            {{ $estudio->horizonte_anios }}
                        </h3>

                        <span>años</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">TMAR</small>

                        <h3 class="text-primary mt-2">
                            {{ number_format($estudio->tasa_descuento, 4) }}%
                        </h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Impuesto</small>

                        <h3 class="text-primary mt-2">
                            {{ number_format($estudio->tasa_impuesto, 4) }}%
                        </h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Inflación</small>

                        <h3 class="text-primary mt-2">
                            {{ number_format($estudio->tasa_inflacion, 4) }}%
                        </h3>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-journal-text"></i>
                Información complementaria
            </h5>
        </div>

        <div class="card-body">

            <strong>Supuestos económicos</strong>

            <p>{{ $estudio->supuestos ?: 'Sin supuestos registrados' }}</p>

            <hr>

            <strong>Observaciones</strong>

            <p>{{ $estudio->observaciones ?: 'Sin observaciones' }}</p>

        </div>

    </div>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-diagram-3"></i>
                Alternativas de inversión
            </h5>
        </div>

        <div class="card-body">

            @forelse($estudio->alternativas as $alternativa)

                <div class="border rounded p-3 mb-2">
                    {{ $alternativa->nombre }}
                </div>

            @empty

                <div class="text-center py-4">

                    <i class="bi bi-folder2-open fs-1 text-muted"></i>

                    <p class="text-muted mt-3">
                        Este estudio todavía no tiene alternativas registradas.
                    </p>

                </div>

            @endforelse

            <button class="btn btn-outline-secondary" disabled>
                <i class="bi bi-plus-circle"></i>
                Agregar alternativa
            </button>

        </div>

    </div>

</div>

@endsection
