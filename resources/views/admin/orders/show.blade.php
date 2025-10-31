@extends('layouts.admin')

@section('title', 'Detalle de Orden - Admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.orders') }}" class="text-yellow-600 hover:text-yellow-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a Órdenes
        </a>
        <h1 class="text-3xl font-bold text-gray-800 mt-2">Orden {{ $order->order_number }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detalles de la orden -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Información de la Orden</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Número de Orden</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Fecha de Creación</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Método de Pago</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->payment_method_text }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estado de Pago</p>
                        @php
                            $paymentColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                            ];
                            $paymentColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                            $paymentTexts = [
                                'pending' => 'Pendiente',
                                'paid' => 'Pagado',
                                'failed' => 'Fallido',
                            ];
                            $paymentText = $paymentTexts[$order->payment_status] ?? $order->payment_status;
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $paymentColor }}">
                            {{ $paymentText }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Información del Cliente</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nombre Completo</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Correo Electrónico</p>
                            <p class="text-gray-900 font-medium">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Teléfono</p>
                            <p class="text-gray-900 font-medium">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Dirección de Envío</p>
                        <p class="text-gray-900 font-medium">{{ $order->customer_address }}</p>
                        <p class="text-gray-900">{{ $order->customer_city }}, {{ $order->customer_state }} {{ $order->customer_zip }}</p>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Productos</h2>
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
                                    <td class="px-6 py-4">
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

            <!-- Notas -->
            @if($order->notes)
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Notas del Cliente</h3>
                    <p class="text-blue-800">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Panel lateral -->
        <div class="lg:col-span-1">
            <!-- Estado de la orden -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Estado de la Orden</h2>
                
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                
                <div class="mb-4">
                    <span class="inline-flex px-4 py-2 text-lg font-semibold rounded-full {{ $statusColor }}">
                        {{ $order->status_text }}
                    </span>
                </div>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar Estado</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded mb-3 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    
                    <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded">
                        Actualizar Estado
                    </button>
                </form>
            </div>

            <!-- Resumen -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total de Items:</span>
                        <span class="font-semibold text-gray-900">{{ $order->items->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cantidad Total:</span>
                        <span class="font-semibold text-gray-900">{{ $order->items->sum('quantity') }}</span>
                    </div>
                    <div class="border-t border-gray-300 pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="text-gray-900 font-bold">Total Orden:</span>
                            <span class="text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
