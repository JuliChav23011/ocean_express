
@extends('layouts.app')

@section('title', 'Administración - Ocean Express')

@section('content')

<div class="container-fluid">

    <h1 class="page-title">Administración</h1>
    <p class="page-subtitle">
        Configuración y gestión general de Ocean Express.
    </p>

    <ul class="nav ocean-tabs" id="adminTabs" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="organizacion-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#organizacion"
                    type="button"
                    role="tab">
                Organización
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="instalaciones-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#instalaciones"
                    type="button"
                    role="tab">
                Instalaciones
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link active"
                    id="usuarios-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#usuarios"
                    type="button"
                    role="tab"
                    aria-selected="true">
                Usuarios y roles
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="finanzas-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#finanzas"
                    type="button"
                    role="tab">
                Finanzas
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="integraciones-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#integraciones"
                    type="button"
                    role="tab">
                Integraciones
            </button>
        </li>

    </ul>

    <div class="tab-content ocean-tab-content" id="adminTabsContent">

        <div class="tab-pane fade"
             id="organizacion" role="tabpanel">
            <h5>Organización</h5>
            <p>Información general de Ocean Express.</p>
        </div>

        <div class="tab-pane fade"
             id="instalaciones" role="tabpanel">
            <h5>Instalaciones</h5>
            <p>Gestión de instalaciones y ubicaciones.</p>
        </div>

        <div class="tab-pane fade show active"
             id="usuarios" role="tabpanel">
            <h5>Usuarios y roles</h5>
            <p>Administración de usuarios y permisos.</p>
        </div>

        <div class="tab-pane fade"
             id="finanzas" role="tabpanel">
            <h5>Finanzas</h5>
            <p>Configuraciones financieras generales.</p>
        </div>

        <div class="tab-pane fade"
             id="integraciones" role="tabpanel">
            <h5>Integraciones</h5>
            <p>Configuración de integraciones del sistema.</p>
        </div>

    </div>

</div>

@endsection
