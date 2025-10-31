@extends('layouts.app')

@section('title', 'Confirmación de Orden - DeWalt Accesorios')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Mensaje de éxito o error -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-8 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-green-800">¡Gracias por tu compra!</h1>
                    <p class="text-green-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @elseif(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-8 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-red-800">Error en el pago</h1>
                    <p class="text-red-700 mt-1">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @elseif(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-yellow-800">Orden Pendiente</h1>
                    <p class="text-yellow-700 mt-1">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-8 rounded-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-green-800">¡Gracias por tu compra!</h1>
                    <p class="text-green-700 mt-1">Tu orden ha sido procesada</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de la orden -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="bg-yellow-400 px-6 py-4">
            <h2 class="text-xl font-bold text-black">Detalles de la Orden</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Número de Orden</h3>
                    <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Fecha</h3>
                    <p class="text-lg text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Estado</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        {{ $order->status_text }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Método de Pago</h3>
                    <p class="text-lg text-gray-900">{{ $order->payment_method_text }}</p>
                </div>

                @if($order->payment_status === 'paid')
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Estado de Pago</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✓ Pagado
                        </span>
                    </div>
                @elseif($order->payment_status === 'failed')
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Estado de Pago</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ✗ Fallido
                        </span>
                    </div>
                @else
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Estado de Pago</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            ⏱ Pendiente
                        </span>
                    </div>
                @endif
            </div>

            @if($order->wompi_codigo_autorizacion)
                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                    <h3 class="text-sm font-semibold text-green-900 mb-2">Información del Pago</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <p class="text-sm text-green-700">Código de Autorización</p>
                            <p class="text-green-900 font-mono font-bold">{{ $order->wompi_codigo_autorizacion }}</p>
                        </div>
                        @if($order->wompi_transaccion_id)
                            <div>
                                <p class="text-sm text-green-700">ID de Transacción</p>
                                <p class="text-green-900 font-mono text-sm">{{ $order->wompi_transaccion_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Información del cliente -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Información del Cliente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nombre</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Teléfono</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Departamento</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_departamento }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Municipio</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_municipio }}</p>
                    </div>
                    @if($order->customer_city)
                    <div>
                        <p class="text-sm text-gray-600">Ciudad/Zona</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_city }}</p>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Dirección de Envío</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos ordenados -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Productos Ordenados</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->accessory_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $item->accessory_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-gray-900">${{ number_format($item->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Subtotal:</td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-700">IVA (13%):</td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->tax, 2) }}</td>
                    </tr>
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="4" class="px-6 py-4 text-right text-lg font-bold text-gray-900">Total:</td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Información adicional -->
    @if($order->notes)
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Notas del Pedido</h3>
            <p class="text-blue-800">{{ $order->notes }}</p>
        </div>
    @endif

    <!-- Acciones -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <p class="text-gray-700 mb-4">
                Hemos enviado un correo de confirmación a <strong>{{ $order->customer_email }}</strong> con todos los detalles de tu orden.
            </p>
            <p class="text-gray-600 mb-6">
                Puedes imprimir esta página para tus registros.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded-lg inline-block">
                    Continuar Comprando
                </a>
                <button onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-8 rounded-lg">
                    Imprimir Orden
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }
    }
</style>
@endsection
