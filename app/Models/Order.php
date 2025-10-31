<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_departamento',
        'customer_municipio',
        'subtotal',
        'tax',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'notes',
        'wompi_enlace_id',
        'wompi_transaccion_id',
        'wompi_codigo_autorizacion',
        'wompi_url_pago',
        'wompi_fecha_pago',
        'verification_code'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'wompi_fecha_pago' => 'datetime',
    ];

    /**
     * Relación con los items de la orden
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generar número de orden único
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Obtener el badge de estado
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    /**
     * Obtener el texto del estado en español
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Pendiente',
            'processing' => 'En Proceso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ];

        return $texts[$this->status] ?? $this->status;
    }

    /**
     * Obtener el texto del método de pago en español
     */
    public function getPaymentMethodTextAttribute()
    {
        $texts = [
            'credit_card' => 'Tarjeta de Crédito',
            'debit_card' => 'Tarjeta de Débito',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Transferencia Bancaria',
        ];

        return $texts[$this->payment_method] ?? $this->payment_method;
    }
}
