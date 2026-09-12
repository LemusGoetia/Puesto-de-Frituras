<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'tipo',
        'precio_por_kilo',
        'stock_gramos',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }
}