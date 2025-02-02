<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = "wallets";
    protected $fillable = [
        'client_id',
        'balance'
    ];

    // Relación inversa, cada billetera pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
