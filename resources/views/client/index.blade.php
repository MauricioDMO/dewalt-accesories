@extends('layouts.app')

@section('title', 'Catálogo de Accesorios DeWalt')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Accesorios DeWALT</h1>
        <p class="mt-2 text-gray-600">Encuentra los mejores accesorios para tus herramientas</p>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('home') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Búsqueda -->
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Buscar por nombre o código..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>

            <!-- Categoría -->
            <div>
                <select name="category" id="category" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subcategoría (se muestra dinámicamente) -->
            <div id="subcategoryContainer" class="{{ request('category') ? '' : 'hidden' }}">
                <select name="subcategory" id="subcategory" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Todas las subcategorías</option>
                    @foreach($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}" {{ request('subcategory') == $subcategory->id ? 'selected' : '' }}>
                            {{ $subcategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botón buscar -->
            <div>
                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded">
                    Buscar
                </button>
            </div>
        </form>

        <!-- Filtros activos -->
        @if(request('search') || request('category') || request('subcategory'))
            <div class="mt-4 flex flex-wrap gap-2 items-center">
                <span class="text-sm text-gray-600 font-medium">Filtros activos:</span>
                
                @if(request('search'))
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Búsqueda: "{{ request('search') }}"
                        <a href="{{ route('home', request()->except('search')) }}" class="hover:text-yellow-900">✕</a>
                    </span>
                @endif
                
                @if(request('category'))
                    @php
                        $selectedCategory = $categories->firstWhere('id', request('category'));
                    @endphp
                    @if($selectedCategory)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Categoría: {{ $selectedCategory->name }}
                            <a href="{{ route('home', request()->except(['category', 'subcategory'])) }}" class="hover:text-blue-900">✕</a>
                        </span>
                    @endif
                @endif
                
                @if(request('subcategory'))
                    @php
                        $selectedSubcategory = $subcategories->firstWhere('id', request('subcategory'));
                    @endphp
                    @if($selectedSubcategory)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Subcategoría: {{ $selectedSubcategory->name }}
                            <a href="{{ route('home', request()->except('subcategory')) }}" class="hover:text-green-900">✕</a>
                        </span>
                    @endif
                @endif
            </div>
        @endif

        <!-- Ordenamiento -->
        <div class="mt-4 flex justify-between items-center flex-wrap gap-2">
            <div class="flex items-center gap-4">
                <p class="text-sm text-gray-600">{{ $accessories->total() }} productos encontrados</p>
                @if(request('search') || request('category') || request('subcategory'))
                    <a href="{{ route('home') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                        ✕ Limpiar filtros
                    </a>
                @endif
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('home', array_merge(request()->except('sort', 'order'), ['sort' => 'name', 'order' => 'asc'])) }}" 
                    class="text-sm {{ request('sort') == 'name' || !request('sort') ? 'text-yellow-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Nombre A-Z
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('home', array_merge(request()->except('sort', 'order'), ['sort' => 'price', 'order' => 'asc'])) }}" 
                    class="text-sm {{ request('sort') == 'price' && request('order') == 'asc' ? 'text-yellow-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Precio: Menor a Mayor
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('home', array_merge(request()->except('sort', 'order'), ['sort' => 'price', 'order' => 'desc'])) }}" 
                    class="text-sm {{ request('sort') == 'price' && request('order') == 'desc' ? 'text-yellow-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    Precio: Mayor a Menor
                </a>
            </div>
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($accessories as $accessory)
        <div class="bg-white rounded-lg shadow hover:shadow-xl transition-shadow duration-300">
            <a href="{{ route('accessory.show', $accessory) }}">
                <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                    @if($accessory->image)
                        <img src="{{ $accessory->image_url }}" alt="{{ $accessory->alt }}" 
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22200%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen%3C/text%3E%3C/svg%3E'">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-gray-400">Sin imagen</span>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-500 mb-1">{{ $accessory->code }}</p>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 h-10">{{ $accessory->name }}</h3>
                    
                    @if($accessory->category)
                        <p class="text-xs text-gray-500 mb-2">{{ $accessory->category->name }}</p>
                    @endif

                    <div class="flex items-center justify-between">
                        @if($accessory->hasOffer())
                            <div class="flex flex-col">
                                <span class="text-xs line-through text-gray-400">${{ number_format($accessory->price, 2) }}</span>
                                <span class="text-lg font-bold text-red-600">${{ number_format($accessory->offer, 2) }}</span>
                            </div>
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">OFERTA</span>
                        @else
                            <span class="text-lg font-bold text-gray-900">${{ number_format($accessory->price, 2) }}</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">No se encontraron productos</p>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-8">
        {{ $accessories->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Manejar cambio de categoría
    document.getElementById('category').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategoryContainer = document.getElementById('subcategoryContainer');
        const subcategorySelect = document.getElementById('subcategory');
        
        if (categoryId) {
            // Mostrar el contenedor de subcategorías
            subcategoryContainer.classList.remove('hidden');
            
            // Limpiar opciones anteriores
            subcategorySelect.innerHTML = '<option value="">Cargando...</option>';
            
            // Cargar subcategorías vía AJAX
            fetch(`/admin/subcategories/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="">Todas las subcategorías</option>';
                    
                    if (data.length === 0) {
                        subcategorySelect.innerHTML = '<option value="">Sin subcategorías</option>';
                    } else {
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar subcategorías:', error);
                    subcategorySelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        } else {
            // Ocultar el contenedor de subcategorías
            subcategoryContainer.classList.add('hidden');
            subcategorySelect.innerHTML = '<option value="">Todas las subcategorías</option>';
        }
    });
</script>
@endpush
@endsection
