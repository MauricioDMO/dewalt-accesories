<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'offer',
        'image',
        'alt',
        'type',
        'units',
        'category_id',
        'subcategory_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer' => 'decimal:2',
        'units' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // Accessor para la URL completa de la imagen
    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }
        
        // Si la imagen ya tiene http/https, devolverla tal cual
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        // Caso contrario, añadir el dominio base
        return 'https://construfijaciones.com' . $this->image;
    }

    // Accessor para mostrar el precio con oferta si existe
    public function getFinalPriceAttribute()
    {
        return $this->offer ?? $this->price;
    }

    // Verificar si tiene oferta
    public function hasOffer()
    {
        return $this->offer !== null && $this->offer > 0;
    }
}
