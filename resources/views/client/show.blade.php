@extends('layouts.app')

@section('title', $accessory->name . ' - DeWalt')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-yellow-600">Inicio</a></li>
            <li>/</li>
            @if($accessory->category)
                <li><a href="{{ route('home', ['category' => $accessory->category_id]) }}" class="hover:text-yellow-600">{{ $accessory->category->name }}</a></li>
                <li>/</li>
            @endif
            <li class="text-gray-900">{{ $accessory->code }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Imagen del producto -->
        <div class="bg-white rounded-lg shadow p-6">
            @if($accessory->image)
                <img src="{{ $accessory->image_url }}" alt="{{ $accessory->alt }}" 
                    class="w-full h-auto rounded"
                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22400%22%3E%3Crect fill=%22%23ddd%22 width=%22400%22 height=%22400%22/%3E%3Ctext fill=%22%23999%22 font-size=%2224%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen disponible%3C/text%3E%3C/svg%3E'">
            @else
                <div class="w-full aspect-square bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-400 text-xl">Sin imagen disponible</span>
                </div>
            @endif
        </div>

        <!-- Información del producto -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-2">Código: <span class="font-semibold">{{ $accessory->code }}</span></p>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $accessory->name }}</h1>
                
                @if($accessory->category)
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $accessory->category->name }}
                        </span>
                        @if($accessory->subcategory)
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $accessory->subcategory->name }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Precio -->
            <div class="border-t border-b border-gray-200 py-6 mb-6">
                @if($accessory->hasOffer())
                    <div class="mb-2">
                        <span class="text-2xl line-through text-gray-400">${{ number_format($accessory->price, 2) }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-4xl font-bold text-red-600">${{ number_format($accessory->offer, 2) }}</span>
                        <span class="bg-red-500 text-white text-sm font-bold px-3 py-1 rounded">
                            {{ round((($accessory->price - $accessory->offer) / $accessory->price) * 100) }}% OFF
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Ahorras: ${{ number_format($accessory->price - $accessory->offer, 2) }}
                    </p>
                @else
                    <span class="text-4xl font-bold text-gray-900">${{ number_format($accessory->price, 2) }}</span>
                @endif

                @if($accessory->units)
                    <p class="text-sm text-gray-600 mt-2">Precio por unidad</p>
                @endif
            </div>

            <!-- Descripción -->
            @if($accessory->description)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h2>
                    <p class="text-gray-700">{{ $accessory->description }}</p>
                </div>
            @endif

            <!-- Información adicional -->
            <div class="bg-gray-50 rounded p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Información del producto</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><strong>Código:</strong> {{ $accessory->code }}</li>
                    @if($accessory->type)
                        <li><strong>Tipo:</strong> {{ $accessory->type }}</li>
                    @endif
                    @if($accessory->category)
                        <li><strong>Categoría:</strong> {{ $accessory->category->name }}</li>
                    @endif
                    @if($accessory->subcategory)
                        <li><strong>Subcategoría:</strong> {{ $accessory->subcategory->name }}</li>
                    @endif
                </ul>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6">
                <a href="{{ route('home') }}" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                    ← Volver al catálogo
                </a>
            </div>
        </div>
    </div>

    <!-- Productos relacionados -->
    @if($relatedAccessories->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Productos Relacionados</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedAccessories as $related)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition-shadow duration-300">
                    <a href="{{ route('accessory.show', $related) }}">
                        <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                            @if($related->image)
                                <img src="{{ $related->image_url }}" alt="{{ $related->alt }}" 
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22200%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen%3C/text%3E%3C/svg%3E'">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-gray-500 mb-1">{{ $related->code }}</p>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 h-10">{{ $related->name }}</h3>
                            
                            <div class="flex items-center justify-between">
                                @if($related->hasOffer())
                                    <div class="flex flex-col">
                                        <span class="text-xs line-through text-gray-400">${{ number_format($related->price, 2) }}</span>
                                        <span class="text-lg font-bold text-red-600">${{ number_format($related->offer, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
