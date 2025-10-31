<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiService
{
    private $apiUrl;
    private $appId;
    private $apiSecret;

    public function __construct()
    {
        $this->apiUrl = config('services.wompi.api_url');
        $this->appId = config('services.wompi.app_id');
        $this->apiSecret = config('services.wompi.api_secret');
    }

    /**
     * Obtiene un nuevo token de acceso OAuth2 en cada llamada
     * El token NO se guarda en caché porque expira rápidamente
     * Según documentación oficial de Wompi: https://id.wompi.sv/connect/token
     */
    private function getAccessToken()
    {
        try {
            // En desarrollo local puede necesitar withoutVerifying()
            // En PRODUCCIÓN siempre verificar SSL
            $httpClient = Http::asForm();
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            Log::info('Solicitando nuevo token de acceso a Wompi');
            
            $response = $httpClient->post('https://id.wompi.sv/connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->appId,
                'client_secret' => $this->apiSecret,
                'audience' => 'wompi_api' // Parámetro correcto según docs oficiales
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Token de acceso obtenido exitosamente', [
                    'expires_in' => $data['expires_in'] ?? 'unknown'
                ]);
                return $data['access_token'];
            }

            Log::error('Error obteniendo token Wompi', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Excepción obteniendo token Wompi', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Crea un enlace de pago en Wompi
     */
    public function crearEnlacePago($datos)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo autenticar con Wompi'
            ];
        }

        try {
            $httpClient = Http::withToken($token);
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            // Preparar payload base
            $payload = [
                'idAplicativo' => $this->appId,
                'identificadorEnlaceComercio' => 'ORDER-' . time() . '-' . rand(1000, 9999),
                'monto' => (float) $datos['monto'],
                'nombreProducto' => $datos['nombreProducto'] ?? 'Producto',
                'formaPago' => [
                    'permitirTarjetaCreditoDebido' => true,
                    'permitirPagoConPuntoAgricola' => false,
                    'permitirPagoEnCuotasAgricola' => false,
                    'permitirPagoEnBitcoin' => false,
                    'permitePagoQuickPay' => false
                ],
                'configuracion' => [
                    'esMontoEditable' => false,
                    'esCantidadEditable' => false,
                    'cantidadPorDefecto' => 1,
                    'duracionInterfazIntentoMinutos' => 30,
                    'notificarTransaccionCliente' => true
                ],
                'vigencia' => [
                    'fechaInicio' => now()->toIso8601String(),
                    'fechaFin' => now()->addDays(7)->toIso8601String()
                ],
                'limitesDeUso' => [
                    'cantidadMaximaPagosExitosos' => 1,
                    'cantidadMaximaPagosFallidos' => 3
                ]
            ];

            // Agregar urlRedirect y urlRetorno SIEMPRE (incluso si es local para desarrollo)
            if (!empty($datos['urlRedirect']) && filter_var($datos['urlRedirect'], FILTER_VALIDATE_URL)) {
                $payload['configuracion']['urlRedirect'] = $datos['urlRedirect'];
                $payload['configuracion']['urlRetorno'] = $datos['urlRedirect'];
                Log::info('URLs de redirect agregadas al payload', ['url' => $datos['urlRedirect']]);
            }

            // Solo agregar webhook si NO es local (Wompi rechaza webhooks locales con 403)
            if (!empty($datos['configuracion']['urlWebhook'])) {
                $webhookUrl = $datos['configuracion']['urlWebhook'];
                $isLocalUrl = str_contains($webhookUrl, 'localhost') || 
                              str_contains($webhookUrl, '127.0.0.1') || 
                              str_contains($webhookUrl, '::1');
                
                if (!$isLocalUrl && filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
                    $payload['configuracion']['urlWebhook'] = $webhookUrl;
                    Log::info('URL de webhook agregada al payload', ['url' => $webhookUrl]);
                } else {
                    Log::info('Webhook local omitido (solo se usa en producción)', ['url' => $webhookUrl]);
                }
            }

            // Agregar email solo si es válido
            if (!empty($datos['configuracion']['emailsNotificacion']) && filter_var($datos['configuracion']['emailsNotificacion'], FILTER_VALIDATE_EMAIL)) {
                $payload['configuracion']['emailsNotificacion'] = $datos['configuracion']['emailsNotificacion'];
            }

            // Agregar infoProducto solo si hay descripción válida
            if (!empty($datos['descripcion'])) {
                $payload['infoProducto'] = [
                    'descripcionProducto' => $datos['descripcion']
                ];
            }

            // Agregar datos adicionales solo si existen
            if (!empty($datos['datosAdicionales']) && is_array($datos['datosAdicionales'])) {
                $payload['datosAdicionales'] = $datos['datosAdicionales'];
            }

            Log::info('Creando enlace de pago Wompi', ['payload' => $payload]);

            $response = $httpClient->post($this->apiUrl . '/EnlacePago', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            Log::error('Error creando enlace Wompi', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear el enlace de pago',
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Excepción creando enlace Wompi', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consulta el estado de un enlace de pago
     */
    public function consultarEnlacePago($idEnlace)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo autenticar con Wompi'
            ];
        }

        try {
            $httpClient = Http::withToken($token);
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->apiUrl . '/EnlacePago/' . $idEnlace);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'No se pudo consultar el enlace de pago'
            ];
        } catch (\Exception $e) {
            Log::error('Excepción consultando enlace Wompi', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al consultar el pago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consulta una transacción específica
     */
    public function consultarTransaccion($idTransaccion)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo autenticar con Wompi'
            ];
        }

        try {
            $httpClient = Http::withToken($token);
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->apiUrl . '/TransaccionCompra/' . $idTransaccion);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'No se pudo consultar la transacción'
            ];
        } catch (\Exception $e) {
            Log::error('Excepción consultando transacción Wompi', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al consultar la transacción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene información del aplicativo
     */
    public function obtenerAplicativo()
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo autenticar con Wompi'
            ];
        }

        try {
            $httpClient = Http::withToken($token);
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->apiUrl . '/Aplicativo');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'No se pudo obtener información del aplicativo'
            ];
        } catch (\Exception $e) {
            Log::error('Excepción obteniendo aplicativo Wompi', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al obtener información: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea una transacción de compra con 3DS
     */
    public function crearTransaccion3DS($datos)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo autenticar con Wompi'
            ];
        }

        try {
            $httpClient = Http::withToken($token);
            
            if (config('app.env') === 'local') {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $payload = [
                'tarjetaCreditoDebido' => [
                    'numeroTarjeta' => $datos['numeroTarjeta'],
                    'cvv' => $datos['cvv'],
                    'mesVencimiento' => (int) $datos['mesVencimiento'],
                    'anioVencimiento' => (int) $datos['anioVencimiento']
                ],
                'monto' => (float) $datos['monto'],
                'urlRedirect' => $datos['urlRedirect'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email' => $datos['email'],
                'ciudad' => $datos['ciudad'],
                'direccion' => $datos['direccion'],
                'idPais' => $datos['idPais'] ?? 'SV', // El Salvador por defecto
                'idRegion' => $datos['idRegion'], // Código ISO 3166-2
                'codigoPostal' => $datos['codigoPostal'] ?? '00000',
                'telefono' => $datos['telefono']
            ];

            // Agregar configuración si se proporciona
            if (isset($datos['configuracion'])) {
                $payload['configuracion'] = $datos['configuracion'];
            }

            // Agregar datos adicionales si se proporcionan
            if (isset($datos['datosAdicionales'])) {
                $payload['datosAdicionales'] = $datos['datosAdicionales'];
            }

            $response = $httpClient->post($this->apiUrl . '/TransaccionCompra/3DS', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Error creando transacción 3DS Wompi', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => 'No se pudo procesar la transacción: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Excepción creando transacción 3DS Wompi', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al procesar la transacción: ' . $e->getMessage()
            ];
        }
    }
}
