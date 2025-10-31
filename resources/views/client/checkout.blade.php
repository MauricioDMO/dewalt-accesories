@extends('layouts.app')

@section('title', 'Checkout - DeWalt Accesorios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Finalizar Compra</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cart.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulario de información del cliente -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información Personal -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Información Personal</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                            <input 
                                type="text" 
                                id="customer_name" 
                                name="customer_name" 
                                value="{{ old('customer_name') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                            <input 
                                type="email" 
                                id="customer_email" 
                                name="customer_email" 
                                value="{{ old('customer_email') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                            <input 
                                type="tel" 
                                id="customer_phone" 
                                name="customer_phone" 
                                value="{{ old('customer_phone') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>

                <!-- Dirección de Envío -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Dirección de Envío</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">Dirección Completa *</label>
                            <textarea 
                                id="customer_address" 
                                name="customer_address" 
                                rows="3"
                                required
                                placeholder="Ej: Colonia Escalón, Calle Principal #123, Edificio Torre Vista, Apto 5-B"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                            >{{ old('customer_address') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Incluye colonia, calle, número de casa/edificio y referencias</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="customer_departamento" class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                                <select 
                                    id="customer_departamento" 
                                    name="customer_departamento" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                >
                                    <option value="">Seleccione...</option>
                                    @foreach(config('elsalvador') as $departamento => $municipios)
                                        <option value="{{ $departamento }}" {{ old('customer_departamento') == $departamento ? 'selected' : '' }}>
                                            {{ $departamento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="customer_municipio" class="block text-sm font-medium text-gray-700 mb-2">Municipio *</label>
                                <select 
                                    id="customer_municipio" 
                                    name="customer_municipio" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                >
                                    <option value="">Primero seleccione departamento</option>
                                </select>
                            </div>

                            <div>
                                <label for="customer_city" class="block text-sm font-medium text-gray-700 mb-2">Ciudad/Localidad</label>
                                <input 
                                    type="text" 
                                    id="customer_city" 
                                    name="customer_city" 
                                    value="{{ old('customer_city') }}"
                                    placeholder="Opcional"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-500 mt-1">Ej: Barrio específico o zona</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Método de Pago</h2>
                    
                    <!-- Información de Wompi -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-blue-900 mb-1">Pago Seguro con Wompi</h3>
                                <p class="text-sm text-blue-800">Serás redirigido a nuestra pasarela de pagos segura Wompi para ingresar los datos de tu tarjeta y completar tu compra. Todos los datos son encriptados y protegidos.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Métodos de pago aceptados -->
                    <div class="space-y-3 mb-6">
                        <h3 class="text-md font-semibold text-gray-900">Métodos de Pago Aceptados</h3>
                        
                        <div class="flex items-center p-4 border-2 border-yellow-400 bg-yellow-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Tarjetas de Crédito y Débito</p>
                                <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-gray-600">
                                    Los datos de tu tarjeta se ingresarán de forma segura en la interfaz de Wompi. No almacenamos información de tarjetas en nuestros servidores.
                                </p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method" value="credit_card">

                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas adicionales (Opcional)</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="3"
                            placeholder="Instrucciones especiales de entrega, preferencias, etc."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        >{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Resumen del Pedido</h2>
                    
                    <!-- Lista de productos -->
                    <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        @foreach($cart as $item)
                            <div class="flex justify-between text-sm">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $item['name'] }}</p>
                                    <p class="text-gray-500">Cant: {{ $item['quantity'] }} x ${{ number_format($item['price'], 2) }}</p>
                                </div>
                                <p class="font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-300 pt-4 space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>IVA (13%):</span>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2">
                            <div class="flex justify-between text-xl font-bold text-gray-900">
                                <span>Total:</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg">
                        Confirmar Pedido
                    </button>

                    <a href="{{ route('cart.index') }}" class="block w-full mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg text-center">
                        Volver al Carrito
                    </a>

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-800">
                            <strong>Nota:</strong> Este es un proceso de pago simulado. No se realizará ningún cargo real.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departamentos = @json(config('elsalvador'));
    const departamentoSelect = document.getElementById('customer_departamento');
    const municipioSelect = document.getElementById('customer_municipio');
    const oldMunicipio = "{{ old('customer_municipio') }}";
    
    // Actualizar municipios cuando cambia el departamento
    departamentoSelect.addEventListener('change', function() {
        const departamento = this.value;
        municipioSelect.innerHTML = '<option value="">Seleccione municipio...</option>';
        
        if (departamento && departamentos[departamento]) {
            departamentos[departamento].forEach(function(municipio) {
                const option = document.createElement('option');
                option.value = municipio;
                option.textContent = municipio;
                if (municipio === oldMunicipio) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });
            municipioSelect.disabled = false;
        } else {
            municipioSelect.disabled = true;
        }
    });
    
    // Cargar municipios si hay un departamento seleccionado (por old())
    if (departamentoSelect.value) {
        departamentoSelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection
