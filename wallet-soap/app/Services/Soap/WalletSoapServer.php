<?php

namespace App\Services\Soap;

use App\Models\Client;
use App\Models\Wallet;
use Illuminate\Support\Facades\Validator;

class WalletSoapServer
{
    public function registroCliente($document, $name, $email, $phone)
    {
        $validator = Validator::make([
            'document' => $document,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ], [
            'document' => 'required|unique:clients,document',
            'name' => 'required|string',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors)); // Retorna los errores
        }
        try {
            $client = Client::create([
                'document' => $document,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ]);

            Wallet::create(['client_id' => $client->id]);

            return $this->response(true, '00', 'Cliente registrado con éxito');
        } catch (\Exception $e) {
            return $this->response(false, '01', $e->getMessage());
        }
    }

    public function recargaBilletera($document, $phone, $amount)
    {
        $validator = Validator::make([
            'document' => $document,
            'phone' => $phone,
            'amount' => $amount,
        ], [
            'document' => 'required|exists:clients,document',
            'phone' => 'required|exists:clients,phone',
            'amount' => 'integer',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors)); // Retorna los errores
        }
        try {
            $client = Client::where('document', $document)->where('phone', $phone)->firstOrFail();
            $wallet = Wallet::where('client_id', $client->id)->firstOrFail();
            $wallet->balance += $amount;
            $wallet->save();

            return $this->response(true, '00', 'Recarga exitosa');
        } catch (\Exception $e) {
            return $this->response(false, '02', 'Error al recargar billetera: ' . $e->getMessage());
        }
    }



    // Método de respuesta que puede ser reutilizado
    private function response($success, $code, $message)
    {
        return [
            'success' => $success,
            'cod' => $code,
            'message' => $message,
        ];
    }
}