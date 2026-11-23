<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Ocean Express')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    @include('components.header')

    <div class="ocean-wrapper">

        <div class="d-none d-lg-block ocean-sidebar-container">
            @include('components.sidebar')
        </div>

        <div class="offcanvas offcanvas-start ocean-mobile-menu"
             tabindex="-1"
             id="mobileSidebar"
             aria-labelledby="mobileSidebarLabel">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileSidebarLabel">
                    Ocean Express
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Cerrar menú">
                </button>
            </div>

            <div class="offcanvas-body p-0">
                @include('components.sidebar')
            </div>
        </div>

        <div class="d-flex flex-column flex-grow-1 min-width-0">

            <main class="ocean-content">
                @yield('content')
            </main>

            @include('components.footer')

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
