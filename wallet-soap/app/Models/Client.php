<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = "clients";
    protected $fillable = [
        'document',
        'name',
        'email',
        'phone'
    ];

     // Relación uno a uno con Wallet
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
