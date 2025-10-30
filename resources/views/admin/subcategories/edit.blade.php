@extends('layouts.admin')

@section('title', 'Editar Subcategoría - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Editar Subcategoría</h1>
    <p class="text-gray-600 mt-1">{{ $subcategory->name }}</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.subcategories.update', $subcategory) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría *</label>
            <select name="category_id" id="category_id" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('category_id') border-red-500 @enderror">
                <option value="">Seleccionar categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Selecciona la categoría a la que pertenece esta subcategoría</p>
        </div>

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $subcategory->name) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">El nombre de la subcategoría (ej: "Desbaste", "Corte")</p>
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700">Slug (opcional)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $subcategory->slug) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('slug') border-red-500 @enderror">
            @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Si lo dejas vacío, se generará automáticamente desde el nombre</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-2">Información</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Esta subcategoría pertenece a: <strong>{{ $subcategory->category->name }}</strong></li>
                <li>• Tiene <strong>{{ $subcategory->accessories()->count() }}</strong> accesorio(s) asociado(s)</li>
            </ul>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.subcategories') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancelar
            </a>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded">
                Actualizar Subcategoría
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-generar slug mientras se escribe el nombre
    const originalSlug = '{{ $subcategory->slug }}';
    document.getElementById('name').addEventListener('input', function(e) {
        const slugInput = document.getElementById('slug');
        if (!slugInput.value || slugInput.dataset.auto !== 'false') {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[áàäâ]/g, 'a')
                .replace(/[éèëê]/g, 'e')
                .replace(/[íìïî]/g, 'i')
                .replace(/[óòöô]/g, 'o')
                .replace(/[úùüû]/g, 'u')
                .replace(/ñ/g, 'n')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        if (this.value !== originalSlug) {
            this.dataset.auto = 'false';
        }
    });
</script>
@endpush
@endsection
