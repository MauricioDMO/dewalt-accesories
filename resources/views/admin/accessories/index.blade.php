@extends('layouts.admin')

@section('title', 'Accesorios - Admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Accesorios</h1>
    <a href="{{ route('admin.accessories.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded">
        + Nuevo Accesorio
    </a>
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
@endsection
