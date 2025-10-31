@extends('layouts.app')

@section('title', 'Pago Procesado - DeWalt Accesorios')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($order->payment_status === 'paid')
            <!-- Pago Exitoso -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header con check verde -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">¡Pago Procesado Exitosamente!</h1>
                    <p class="text-green-100 text-lg">Tu pedido ha sido confirmado</p>
                </div>

                <!-- Información del pedido -->
                <div class="px-6 py-8">
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-600">Número de Pedido</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Fecha</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($order->wompi_codigo_autorizacion)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-green-800"><strong>Código de Autorización:</strong> {{ $order->wompi_codigo_autorizacion }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Información del Cliente</h3>
                                <div class="space-y-2 text-sm">
                                    <p><strong>Nombre:</strong> {{ $order->customer_name }}</p>
                                    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                    <p><strong>Teléfono:</strong> {{ $order->customer_phone }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Dirección de Entrega</h3>
                                <div class="space-y-2 text-sm">
                                    <p>{{ $order->customer_address }}</p>
                                    <p>{{ $order->customer_municipio }}, {{ $order->customer_departamento }}</p>
                                    @if($order->customer_city)
                                    <p>{{ $order->customer_city }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items del pedido -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos Ordenados</h3>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $item->accessory_name }}</p>
                                                <p class="text-sm text-gray-500">Código: {{ $item->accessory_code }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-right text-gray-900">${{ number_format($item->price, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Subtotal:</td>
                                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700">IVA (13%):</td>
                                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->tax, 2) }}</td>
                                    </tr>
                                    <tr class="border-t-2 border-gray-300">
                                        <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-gray-900">Total:</td>
                                        <td class="px-6 py-4 text-right text-2xl font-bold text-green-600">${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-2">¿Qué sigue?</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• Recibirás un correo de confirmación con los detalles de tu pedido</li>
                                    <li>• Procesaremos tu orden en las próximas 24-48 horas</li>
                                    <li>• Te contactaremos para coordinar la entrega</li>
                                    <li>• Guarda el número de pedido para seguimiento: <strong>{{ $order->order_number }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('home') }}" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded-lg text-center transition-colors">
                            Continuar Comprando
                        </a>
                        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                            Imprimir Comprobante
                        </button>
                    </div>
                </div>
            </div>

        @else
            <!-- Pago Fallido -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header con X roja -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Pago No Procesado</h1>
                    <p class="text-red-100 text-lg">No se pudo completar la transacción</p>
                </div>

                <div class="px-6 py-8">
                    <div class="mb-6">
                        <p class="text-gray-700 mb-4">Lamentablemente, no pudimos procesar tu pago. Esto puede deberse a:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                            <li>Fondos insuficientes en la cuenta</li>
                            <li>Datos de tarjeta incorrectos</li>
                            <li>Límite de transacción excedido</li>
                            <li>Cancelación del pago</li>
                        </ul>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <p class="text-sm text-yellow-800">
                            <strong>Número de Pedido:</strong> {{ $order->order_number }}<br>
                            Este pedido quedó registrado como <strong>cancelado</strong>. Si deseas realizar tu compra, por favor intenta nuevamente.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('cart.checkout') }}" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded-lg text-center transition-colors">
                            Intentar Nuevamente
                        </a>
                        <a href="{{ route('home') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg text-center transition-colors">
                            Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<style>
@media print {
    nav, footer, button {
        display: none !important;
    }
}
</style>
@endsection
