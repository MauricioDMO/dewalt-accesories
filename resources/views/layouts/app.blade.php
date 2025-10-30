<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DeWalt Accesorios')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <nav class="bg-yellow-400 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-black">
                        DeWALT Accesorios
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-black hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Catálogo
                    </a>
                    @if(session('admin_id'))
                        <a href="{{ route('admin.dashboard') }}" class="text-black hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                            Panel Admin
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-black text-yellow-400 hover:bg-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                                Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-black text-yellow-400 hover:bg-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                            Admin
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <p class="text-center">&copy; 2024 DeWalt Accesorios. Todos los derechos reservados.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
