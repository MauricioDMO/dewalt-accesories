@extends('layouts.admin')

@section('title', 'Accesorios - Admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Accesorios</h1>
    <a href="{{ route('admin.accessories.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded">
        + Nuevo Accesorio
    </a>
</div>

<!-- Filtros y Búsqueda -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.accessories') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-6 gap-4">
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

        <!-- Subcategoría -->
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

        <!-- Ordenar por -->
        <div>
            <select name="sort" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="name" {{ request('sort', 'name') == 'name' ? 'selected' : '' }}>Ordenar por Nombre</option>
                <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>Ordenar por Código</option>
                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Ordenar por Precio</option>
            </select>
        </div>

        <!-- Botón buscar -->
        <div>
            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded">
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
                    <a href="{{ route('admin.accessories', request()->except('search')) }}" class="hover:text-yellow-900">✕</a>
                </span>
            @endif
            
            @if(request('category'))
                @php
                    $selectedCategory = $categories->firstWhere('id', request('category'));
                @endphp
                @if($selectedCategory)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Categoría: {{ $selectedCategory->name }}
                        <a href="{{ route('admin.accessories', request()->except(['category', 'subcategory'])) }}" class="hover:text-blue-900">✕</a>
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
                        <a href="{{ route('admin.accessories', request()->except('subcategory')) }}" class="hover:text-green-900">✕</a>
                    </span>
                @endif
            @endif

            <a href="{{ route('admin.accessories') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                ✕ Limpiar todos los filtros
            </a>
        </div>
    @endif

    <!-- Información de resultados -->
    <div class="mt-4 text-sm text-gray-600">
        Mostrando {{ $accessories->firstItem() ?? 0 }} - {{ $accessories->lastItem() ?? 0 }} de {{ $accessories->total() }} accesorios
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accessories as $accessory)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($accessory->image)
                            <img src="{{ $accessory->image_url }}" alt="{{ $accessory->alt }}" class="h-12 w-12 object-cover rounded">
                        @else
                            <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-400 text-xs">Sin img</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $accessory->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($accessory->name, 40) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $accessory->category->name ?? 'N/A' }}
                        @if($accessory->subcategory)
                            <br><span class="text-xs text-gray-400">{{ $accessory->subcategory->name }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($accessory->hasOffer())
                            <div class="flex flex-col">
                                <span class="line-through text-gray-400 text-xs">${{ number_format($accessory->price, 2) }}</span>
                                <span class="text-red-600 font-bold">${{ number_format($accessory->offer, 2) }}</span>
                            </div>
                        @else
                            ${{ number_format($accessory->price, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.accessories.edit', $accessory) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            <form action="{{ route('admin.accessories.destroy', $accessory) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este accesorio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No hay accesorios registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $accessories->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const subcategoryContainer = document.getElementById('subcategoryContainer');
        const subcategorySelect = document.getElementById('subcategory');

        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            if (categoryId) {
                subcategoryContainer.classList.remove('hidden');
                
                // Limpiar opciones actuales excepto la primera
                subcategorySelect.innerHTML = '<option value="">Todas las subcategorías</option>';
                
                // Cargar subcategorías via AJAX
                fetch(`/admin/subcategories/${categoryId}`)
                    .then(response => response.json())
                    .then(subcategories => {
                        subcategories.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            subcategorySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar subcategorías:', error);
                    });
            } else {
                subcategoryContainer.classList.add('hidden');
                subcategorySelect.value = '';
            }
        });
    });
</script>
@endsection
