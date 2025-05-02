<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('CLIENT_NAME', 'Stock') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon-Loja.png') }}">
    @yield('scripts')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    @livewireStyles
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ asset('css/bazar.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos específicos para o header */
        /* img {
            width: 80px;
        } */
        .navbar {
            padding: 1.2rem 0;
            min-height: 70px;
        }

        .navbar-brand {
            padding: 0;
            margin-left: 1.5rem;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .navbar-nav {
            gap: 0.8rem;
        }

        .nav-link {
            font-weight: 400;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 1.05rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Ajustes para mobile */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 1rem 0;
            }

            .navbar-brand {
                margin-left: 1rem;
            }

            .navbar-collapse {
                padding: 1rem;
                background-color: rgba(0, 0, 0, 0.05);
                margin-top: 0.5rem;
                border-radius: 0 0 8px 8px;
            }

            .navbar-nav {
                margin-right: 0 !important;
                gap: 0.5rem;
            }

            .nav-link {
                padding: 0.8rem 1rem;
            }

            .navbar-toggler {
                border-color: white;
                margin-right: 1rem;
                border: none;
                padding: 0.5rem;
            }
            .navbar-toggler-icon {
                filter: invert(1) brightness(10); /* Quanto maior o número, mais branco */
            }

            .navbar-toggler:focus {
                box-shadow: none;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="full-app-container">
        <nav class="navbar navbar-expand-lg navbar-light bg-primary">
            <div class="container-fluid px-0">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('img/favicon-Loja.png') }}" alt="Logo">
                </a>
                <button class="navbar-toggler text-white" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto me-4">
                        @auth
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('categorias.index') }}">Categorias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('produtos.index') }}">Produtos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('entidades.index') }}">Entidades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('vendas.index') }}">Planejamento</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('vendas.buscar') }}">Relatórios</a>
                        </li>

                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link text-white bg-transparent border-0">Sair</button>
                            </form>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">Login</a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            @yield('content')
            @livewireScripts
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.getElementById('navbarSupportedContent');

    // Verifica se Bootstrap está disponível
    if (typeof bootstrap !== 'undefined' && navbarCollapse) {
        let bsCollapse = new bootstrap.Collapse(navbarCollapse, {
            toggle: false
        });

        // Alternar manualmente (abre/fecha)
        navbarToggler?.addEventListener('click', function () {
            if (navbarCollapse.classList.contains('show')) {
                bsCollapse.hide();
            } else {
                bsCollapse.show();
            }
        });

        // Fecha o menu ao clicar em um item, apenas em telas menores
        if (window.innerWidth < 992) {
            document.querySelectorAll('.nav-link').forEach(function (navItem) {
                navItem.addEventListener('click', function () {
                    if (navbarCollapse.classList.contains('show')) {
                        bsCollapse.hide();
                    }
                });
            });
        }
    } else {
        console.error('Bootstrap não está carregado ou elemento navbar não encontrado');
        // Fallback básico caso Bootstrap não esteja disponível
        navbarToggler?.addEventListener('click', function () {
            navbarCollapse?.classList.toggle('show');
        });
    }
});
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
