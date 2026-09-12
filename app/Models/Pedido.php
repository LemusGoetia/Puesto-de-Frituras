<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'fecha',
        'estado',
        'total',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }
}