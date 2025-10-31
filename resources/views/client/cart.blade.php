@extends('layouts.app')

@section('title', 'Carrito de Compras - DeWalt Accesorios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Carrito de Compras</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($cart))
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-500 mb-6">Agrega productos para comenzar a comprar</p>
            <a href="{{ route('home') }}" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg">
                Ir al Catálogo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lista de productos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @foreach($cart as $id => $item)
                        <div class="p-6 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center gap-4">
                                <!-- Imagen del producto -->
                                <div class="w-24 h-24 flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{'https://construfijaciones.com' . $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-400">Sin imagen</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Información del producto -->
                                <div class="flex-grow">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500">Código: {{ $item['code'] }}</p>
                                    <p class="text-lg font-bold text-yellow-600 mt-2">${{ number_format($item['price'], 2) }}</p>
                                </div>

                                <!-- Cantidad y acciones -->
                                <div class="flex items-center gap-4">
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <label for="quantity-{{ $id }}" class="text-sm text-gray-700">Cantidad:</label>
                                        <input 
                                            type="number" 
                                            id="quantity-{{ $id }}"
                                            name="quantity" 
                                            value="{{ $item['quantity'] }}" 
                                            min="1" 
                                            max="99"
                                            class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                            onchange="this.form.submit()"
                                        >
                                    </form>

                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Subtotal:</p>
                                        <p class="text-lg font-bold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                    </div>

                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Resumen del Pedido</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>${{ number_format($total / 1.13, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>IVA (13%):</span>
                            <span>${{ number_format($total - ($total / 1.13), 2) }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 mt-2">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total:</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('cart.checkout') }}" class="block w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg text-center">
                        Proceder al Pago
                    </a>

                    <a href="{{ route('home') }}" class="block w-full mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg text-center">
                        Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
