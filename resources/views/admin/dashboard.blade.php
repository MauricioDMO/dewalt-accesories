@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Accesorios -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Total Accesorios</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalAccessories }}</p>
            </div>
        </div>
    </div>

    <!-- Total Categorías -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Categorías</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalCategories }}</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.categories') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Gestionar →
            </a>
        </div>
    </div>

    <!-- Total Subcategorías -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Subcategorías</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalSubcategories }}</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.subcategories') }}" class="text-sm text-green-600 hover:text-green-800">
                Gestionar →
            </a>
        </div>
    </div>
</div>

<!-- Accesorios Recientes -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Accesorios Recientes</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentAccessories as $accessory)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $accessory->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($accessory->name, 50) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $accessory->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($accessory->hasOffer())
                            <span class="line-through text-gray-400">${{ number_format($accessory->price, 2) }}</span>
                            <span class="text-red-600 font-bold ml-2">${{ number_format($accessory->offer, 2) }}</span>
                        @else
                            ${{ number_format($accessory->price, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="{{ route('admin.accessories.edit', $accessory) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        <a href="{{ route('admin.accessories') }}" class="text-yellow-600 hover:text-yellow-900 font-medium">
            Ver todos los accesorios →
        </a>
    </div>
</div>
@endsection
