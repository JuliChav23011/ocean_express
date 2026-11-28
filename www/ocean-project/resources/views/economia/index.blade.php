
@extends('layouts.app')

@section('title', 'Ingeniería Económica')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h1 class="fw-bold">Ingeniería Económica</h1>
        <p class="text-muted">
            Evaluación financiera y análisis de alternativas de inversión.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <i class="bi bi-clipboard-data fs-1 text-primary"></i>

                    <h5 class="card-title mt-3">
                        Estudios de inversión
                    </h5>

                    <p class="card-text text-muted">
                        Registro y administración de estudios económicos.
                    </p>

                    <a href="{{ route('economia.estudios.index') }}"
                       class="btn btn-primary">
                        Administrar estudios
                    </a>

                </div>
            </div>
        </div>

        @foreach([
            ['bi-diagram-3', 'Alternativas de inversión', 'Registro y comparación de alternativas.'],
            ['bi-cash-stack', 'Flujos de efectivo', 'Administración de ingresos y egresos.'],
            ['bi-calculator', 'Indicadores económicos', 'Cálculo de VPN, TIR, CAUE y B/C.'],
            ['bi-graph-down', 'Depreciación', 'Evaluación de activos y depreciación.'],
            ['bi-bar-chart', 'Comparación económica', 'Análisis y selección de alternativas.']
        ] as $modulo)

        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <i class="bi {{ $modulo[0] }} fs-1 text-primary"></i>

                    <h5 class="card-title mt-3">
                        {{ $modulo[1] }}
                    </h5>

                    <p class="card-text text-muted">
                        {{ $modulo[2] }}
                    </p>

                 @if($modulo[1] === 'Alternativas de inversión')

    <a href="{{ route('economia.alternativas.index') }}"
       class="btn btn-primary">
        Administrar alternativas
    </a>

@else

    <button class="btn btn-outline-secondary" disabled>
        En desarrollo
    </button>

@endif

                </div>
            </div>
        </div>

        @endforeach

    </div>

</div>

@endsection
