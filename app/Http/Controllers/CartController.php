<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Obtener el carrito de la sesión
     */
    private function getCart()
    {
        return Session::get('cart', []);
    }

    /**
     * Guardar el carrito en la sesión
     */
    private function saveCart($cart)
    {
        Session::put('cart', $cart);
    }

    /**
     * Agregar producto al carrito
     */
    public function add(Request $request, $id)
    {
        $accessory = Accessory::findOrFail($id);
        $cart = $this->getCart();

        $quantity = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $accessory->id,
                'name' => $accessory->name,
                'code' => $accessory->code,
                'price' => $accessory->offer > 0 ? $accessory->offer : $accessory->price,
                'image' => $accessory->image_url,
                'quantity' => $quantity
            ];
        }

        $this->saveCart($cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    /**
     * Mostrar el carrito
     */
    public function index()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('client.cart', compact('cart', 'total'));
    }

    /**
     * Actualizar cantidad de un producto
     */
    public function update(Request $request, $id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $quantity = $request->input('quantity', 1);
            if ($quantity > 0) {
                $cart[$id]['quantity'] = $quantity;
            } else {
                unset($cart[$id]);
            }
            $this->saveCart($cart);
        }

        return redirect()->route('cart.index')->with('success', 'Carrito actualizado');
    }

    /**
     * Eliminar producto del carrito
     */
    public function remove($id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->saveCart($cart);
        }

        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito');
    }

    /**
     * Mostrar formulario de checkout
     */
    public function checkout()
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'El carrito está vacío');
        }

        // Los precios ya incluyen IVA del 13%
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Calcular subtotal e IVA desde el total (precios incluyen IVA)
        $subtotal = $total / 1.13; // Total sin IVA
        $tax = $total - $subtotal; // IVA del 13%

        return view('client.checkout', compact('cart', 'subtotal', 'tax', 'total'));
    }

    /**
     * Procesar la orden
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'nullable|string|max:100',
            'customer_departamento' => 'required|string|max:100',
            'customer_municipio' => 'required|string|max:100',
            'payment_method' => 'required|in:credit_card,debit_card,paypal,bank_transfer',
            'notes' => 'nullable|string',
        ]);

        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'El carrito está vacío');
        }

        // Los precios ya incluyen IVA del 13%
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Calcular subtotal e IVA desde el total (precios incluyen IVA)
        $subtotal = $total / 1.13; // Total sin IVA
        $tax = $total - $subtotal; // IVA del 13%

        // Generar código de verificación único
        $verificationCode = \Illuminate\Support\Str::uuid()->toString();

        // Crear la orden primero (sin procesar pago aún)
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_city' => $request->customer_city ?? '',
            'customer_departamento' => $request->customer_departamento,
            'customer_municipio' => $request->customer_municipio,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => $request->notes,
            'verification_code' => $verificationCode
        ]);

        // Crear los items de la orden
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'accessory_id' => $item['id'],
                'accessory_name' => $item['name'],
                'accessory_code' => $item['code'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }

        // Crear enlace de pago con Wompi
        $wompiService = new WompiService();

        // Construir URL de redirect con verificación
        $appUrl = config('app.url');
        $redirectUrl = $appUrl . '/pago-procesado?id=' . $order->order_number . '&code=' . $verificationCode;

        $resultadoWompi = $wompiService->crearEnlacePago([
            'monto' => (float) $total,
            'nombreProducto' => 'Pedido #' . $order->order_number,
            'urlRedirect' => $redirectUrl,
            'configuracion' => [
                'emailsNotificacion' => config('mail.from.address'),
                'urlWebhook' => $appUrl . '/webhook/wompi',
                'notificarTransaccionCliente' => true
            ],
            'datosAdicionales' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'verification_code' => $verificationCode
            ]
        ]);

        if ($resultadoWompi['success']) {
            // Actualizar orden con datos de Wompi
            $order->update([
                'wompi_enlace_id' => $resultadoWompi['data']['idEnlace'],
                'wompi_url_pago' => $resultadoWompi['data']['urlEnlace']
            ]);

            // Limpiar el carrito
            Session::forget('cart');

            // Redirigir a la URL de pago de Wompi
            return redirect($resultadoWompi['data']['urlEnlace']);
        } else {
            // Si falla Wompi, eliminar la orden y mostrar error
            $order->delete();
            
            Log::error('Error creando enlace de pago Wompi', [
                'order_number' => $order->order_number,
                'error' => $resultadoWompi['message']
            ]);

            return redirect()->route('cart.checkout')
                ->with('error', 'Error al procesar el pago: ' . ($resultadoWompi['message'] ?? 'Error desconocido'))
                ->withInput();
        }
    }

    /**
     * Mostrar confirmación de orden
     */
    public function confirmation($id)
    {
        $order = Order::with('items.accessory')->findOrFail($id);
        return view('client.order-confirmation', compact('order'));
    }

    /**
     * Callback de Wompi después del pago
     */
    public function wompiCallback($id, Request $request)
    {
        $order = Order::findOrFail($id);

        // Consultar el estado del enlace de pago en Wompi
        $wompiService = new WompiService();
        $resultado = $wompiService->consultarEnlacePago($order->wompi_enlace_id);

        if ($resultado['success'] && isset($resultado['data'])) {
            $transaccion = $resultado['data'];

            // Actualizar orden con datos de la transacción
            $order->update([
                'wompi_codigo_autorizacion' => $transaccion['codigoAutorizacion'] ?? null,
                'wompi_fecha_pago' => isset($transaccion['fechaTransaccion']) ? 
                    \Carbon\Carbon::parse($transaccion['fechaTransaccion']) : now(),
                'payment_status' => ($transaccion['esAprobada'] ?? false) ? 'paid' : 'failed',
                'status' => ($transaccion['esAprobada'] ?? false) ? 'processing' : 'cancelled'
            ]);

            if ($transaccion['esAprobada']) {
                return redirect()->route('order.confirmation', $order->id)
                    ->with('success', '¡Pago procesado exitosamente!');
            } else {
                return redirect()->route('order.confirmation', $order->id)
                    ->with('error', 'El pago no pudo ser procesado: ' . ($transaccion['mensaje'] ?? 'Error desconocido'));
            }
        }

        // Si no se puede consultar el estado, mostrar la orden como pendiente
        return redirect()->route('order.confirmation', $order->id)
            ->with('warning', 'Orden creada. El estado del pago se actualizará pronto.');
    }

    /**
     * Página de pago procesado (después de Wompi)
     */
    public function paymentProcessed(Request $request)
    {
        $orderNumber = $request->query('id');
        $verificationCode = $request->query('code');

        // Validar que se recibieron los parámetros
        if (!$orderNumber || !$verificationCode) {
            return redirect()->route('home')
                ->with('error', 'Parámetros de verificación inválidos');
        }

        // Buscar la orden con el número y código de verificación
        $order = Order::where('order_number', $orderNumber)
            ->where('verification_code', $verificationCode)
            ->with('items.accessory')
            ->first();

        // Si no se encuentra la orden o el código no coincide
        if (!$order) {
            Log::warning('Intento de acceso con código de verificación inválido', [
                'order_number' => $orderNumber,
                'code' => $verificationCode,
                'ip' => $request->ip()
            ]);

            return redirect()->route('home')
                ->with('error', 'Código de verificación inválido');
        }

        // Consultar estado del pago en Wompi
        $wompiService = new WompiService();
        $resultado = $wompiService->consultarEnlacePago($order->wompi_enlace_id);

        if ($resultado['success'] && isset($resultado['data'])) {
            $enlace = $resultado['data'];

            // Actualizar orden con datos del pago
            $order->update([
                'wompi_codigo_autorizacion' => $enlace['codigoAutorizacion'] ?? null,
                'wompi_fecha_pago' => isset($enlace['fechaTransaccion']) ? 
                    \Carbon\Carbon::parse($enlace['fechaTransaccion']) : now(),
                'payment_status' => ($enlace['esAprobada'] ?? false) ? 'paid' : 'failed',
                'status' => ($enlace['esAprobada'] ?? false) ? 'processing' : 'cancelled'
            ]);

            Log::info('Pago procesado', [
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'wompi_codigo' => $order->wompi_codigo_autorizacion
            ]);
        }

        // Mostrar página de confirmación
        return view('client.payment-processed', compact('order'));
    }

    /**
     * Webhook de Wompi para notificaciones asíncronas
     */
    public function wompiWebhook(Request $request)
    {
        Log::info('Webhook Wompi recibido', $request->all());

        // Aquí puedes procesar notificaciones de Wompi
        // Por ejemplo, cuando cambia el estado de un pago
        
        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Obtener cantidad de items en el carrito
     */
    public function getCartCount()
    {
        $cart = $this->getCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return response()->json(['count' => $count]);
    }
}
