@extends('layouts.app')

@section('title', 'Detalles de la alternativa')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-eye"></i>
                Detalles de la alternativa
            </h2>

            <p class="text-muted mb-0">
                {{ $alternativa->codigo }} -
                {{ $alternativa->nombre }}
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('economia.alternativas.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>

            <a href="{{ route('economia.alternativas.edit', $alternativa) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil-square"></i>
                Editar
            </a>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-info-circle"></i>
                Información general
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-4">
                    <strong>Código</strong>
                    <p>{{ $alternativa->codigo }}</p>
                </div>

                <div class="col-md-8">
                    <strong>Nombre</strong>
                    <p>{{ $alternativa->nombre }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Empresa</strong>
                    <p>{{ $alternativa->estudioInversion->empresa->nombre }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Estudio de inversión</strong>

                    <p>
                        <a href="{{ route('economia.estudios.show', $alternativa->estudioInversion) }}">
                            {{ $alternativa->estudioInversion->nombre }}
                        </a>
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Tipo de alternativa</strong>

                    <p>
                        @switch($alternativa->tipo)

                            @case('compra_activo')
                                Compra de activo
                                @break

                            @case('contratacion_servicio')
                                Contratación de servicio
                                @break

                            @default
                                Otra alternativa

                        @endswitch
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Estado</strong>

                    <p>
                        @if($alternativa->activa)
                            <span class="badge bg-success">
                                Activa
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Inactiva
                            </span>
                        @endif
                    </p>
                </div>

                <div class="col-12">
                    <strong>Descripción</strong>

                    <p>
                        {{ $alternativa->descripcion ?: 'Sin descripción registrada.' }}
                    </p>
                </div>

            </div>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-cash-stack"></i>
                Información financiera
            </h5>

        </div>

        <div class="card-body">

            @php
                $moneda = $alternativa->estudioInversion->moneda;

                $flujoEstimado =
                    $alternativa->ingreso_anual_estimado -
                    $alternativa->costo_operativo_anual;
            @endphp

            <div class="row g-3">

                @foreach([
                    'Inversión inicial' => $alternativa->inversion_inicial,
                    'Costo operativo anual' => $alternativa->costo_operativo_anual,
                    'Ingreso anual estimado' => $alternativa->ingreso_anual_estimado,
                    'Valor residual' => $alternativa->valor_residual
                ] as $concepto => $valor)

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <p class="text-muted mb-1">
                                {{ $concepto }}
                            </p>

                            <h4 class="fw-bold mb-0">
                                {{ $moneda }}
                                {{ number_format($valor, 2) }}
                            </h4>

                        </div>

                    </div>

                @endforeach

                <div class="col-12">

                    <div class="border rounded p-3 bg-light">

                        <p class="text-muted mb-1">
                            Flujo operativo anual estimado antes de impuestos
                        </p>

                        <h4 class="fw-bold mb-0 {{ $flujoEstimado >= 0 ? 'text-success' : 'text-danger' }}">

                            {{ $moneda }}
                            {{ number_format($flujoEstimado, 2) }}

                        </h4>

                        <small class="text-muted">
                            Ingresos anuales menos costos operativos anuales.
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-calculator"></i>
                Parámetros de depreciación
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <strong>Vida útil</strong>

                    <p>
                        @if($alternativa->vida_util_anios)
                            {{ $alternativa->vida_util_anios }} año(s)
                        @else
                            No definida
                        @endif
                    </p>

                </div>

                <div class="col-md-6">

                    <strong>Método de depreciación</strong>

                    <p>

                        @switch($alternativa->metodo_depreciacion)

                            @case('linea_recta')
                                Línea recta
                                @break

                            @case('suma_digitos')
                                Suma de dígitos de los años
                                @break

                            @case('doble_saldo_decreciente')
                                Doble saldo decreciente
                                @break

                            @default
                                No aplica

                        @endswitch

                    </p>

                </div>

            </div>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-check"></i>
                Evidencias y justificación
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <strong>Clasificación de los datos</strong>

                    <p>

                        @switch($alternativa->clasificacion_datos)

                            @case('real')
                                Datos reales
                                @break

                            @case('mixto')
                                Datos reales y estimados
                                @break

                            @default
                                Datos estimados

                        @endswitch

                    </p>

                </div>

                <div class="col-md-6">

                    <strong>Evidencia asociada</strong>

                    <p>

                        @if($alternativa->evidencia)

                            {{ $alternativa->evidencia->codigo }} -
                            {{ $alternativa->evidencia->titulo }}

                        @else

                            Sin evidencia asociada

                        @endif

                    </p>

                </div>

                <div class="col-12">

                    <strong>Justificación</strong>

                    <p>
                        {{ $alternativa->justificacion ?: 'Sin justificación registrada.' }}
                    </p>

                </div>

            </div>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-bar-chart"></i>
                Flujos de efectivo
            </h5>

        </div>

        <div class="card-body">

            <p>
                Flujos registrados:
                <strong>
                    {{ $alternativa->flujosEfectivo->count() }}
                </strong>
            </p>

            <button class="btn btn-outline-primary" disabled>
                <i class="bi bi-plus-circle"></i>
                Administrar flujos de efectivo
            </button>

        </div>

    </div>



    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-graph-up-arrow"></i>
                Evaluaciones económicas
            </h5>

        </div>

        <div class="card-body">

            <p>
                Evaluaciones realizadas:
                <strong>
                    {{ $alternativa->evaluaciones->count() }}
                </strong>
            </p>

            <button class="btn btn-outline-primary" disabled>
                <i class="bi bi-calculator"></i>
                Evaluar alternativa
            </button>

        </div>

    </div>

</div>

@endsection