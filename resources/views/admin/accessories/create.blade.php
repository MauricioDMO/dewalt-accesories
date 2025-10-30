@extends('layouts.admin')

@section('title', 'Crear Accesorio - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Accesorio</h1>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.accessories.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Código -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Código *</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Precio *</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Oferta (opcional) -->
            <div>
                <label for="offer" class="block text-sm font-medium text-gray-700">Precio de Oferta (opcional)</label>
                <input type="number" step="0.01" name="offer" id="offer" value="{{ old('offer') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('offer') border-red-500 @enderror">
                @error('offer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" name="type" id="type" value="{{ old('type', 'Accesorio') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>

            <!-- Units -->
            <div class="flex items-center">
                <input type="checkbox" name="units" id="units" value="1" {{ old('units') ? 'checked' : '' }}
                    class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                <label for="units" class="ml-2 block text-sm text-gray-900">
                    Venta por unidades
                </label>
            </div>

            <!-- Categoría -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select name="category_id" id="category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Seleccionar categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subcategoría -->
            <div>
                <label for="subcategory_id" class="block text-sm font-medium text-gray-700">Subcategoría</label>
                <select name="subcategory_id" id="subcategory_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Seleccionar subcategoría</option>
                </select>
            </div>

            <!-- Imagen -->
            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-medium text-gray-700">URL de Imagen</label>
                <input type="text" name="image" id="image" value="{{ old('image') }}"
                    placeholder="/products/accessory/codigo.webp"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <p class="mt-1 text-sm text-gray-500">Ejemplo: /products/accessory/DW8424.webp (se agregará automáticamente https://construfijaciones.com)</p>
            </div>

            <!-- Alt -->
            <div class="md:col-span-2">
                <label for="alt" class="block text-sm font-medium text-gray-700">Texto Alternativo (Alt)</label>
                <input type="text" name="alt" id="alt" value="{{ old('alt') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.accessories') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancelar
            </a>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded">
                Crear Accesorio
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Cargar subcategorías cuando cambia la categoría
    document.getElementById('category_id').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('subcategory_id');
        
        subcategorySelect.innerHTML = '<option value="">Seleccionar subcategoría</option>';
        
        if (categoryId) {
            fetch(`/admin/subcategories/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategorySelect.appendChild(option);
                    });
                });
        }
    });
</script>
@endpush
