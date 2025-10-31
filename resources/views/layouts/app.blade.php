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
                    <a href="{{ route('cart.index') }}" class="relative text-black hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="ml-1">Carrito</span>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ count(session('cart', [])) }}
                        </span>
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
