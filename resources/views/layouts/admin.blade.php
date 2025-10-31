<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - DeWalt')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-yellow-400">
                        Panel de Administración
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-300">{{ session('admin_name') }}</span>
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        Ver Sitio
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md min-h-screen">
            <nav class="mt-5 px-2">
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'text-yellow-600 bg-yellow-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.accessories') }}" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.accessories*') ? 'text-yellow-600 bg-yellow-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Accesorios
                </a>
                <a href="{{ route('admin.categories') }}" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.categories*') ? 'text-yellow-600 bg-yellow-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Categorías
                </a>
                <a href="{{ route('admin.subcategories') }}" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.subcategories*') ? 'text-yellow-600 bg-yellow-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Subcategorías
                </a>
                <a href="{{ route('admin.orders') }}" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.orders*') ? 'text-yellow-600 bg-yellow-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Órdenes de Compra
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
