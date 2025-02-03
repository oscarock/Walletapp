<?php

namespace App\Services\Soap;

use App\Models\Client;
use App\Models\Token;
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
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors)); // Retorna los errores
        }
        try {
            $client = Client::where('document', $document)->where('phone', $phone)->firstOrFail();
            $wallet = $client->wallet;
            $wallet->balance += $amount;
            $wallet->save();

            return $this->response(true, '00', 'Recarga exitosa');
        } catch (\Exception $e) {
            return $this->response(false, '02', 'Error al recargar billetera: ' . $e->getMessage());
        }
    }

    public function pagar($document, $amount)
    {

        $validator = Validator::make([
            'document' => $document,
            'amount' => $amount,
        ], [
            'document' => 'required|exists:clients,document',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors)); // Retorna los errores
        }
        try {
            $client = Client::where('document', $document)->firstOrFail();
            $wallet = $client->wallet;

            if ($wallet->balance < $amount) {
                return $this->response(false, '03', 'Saldo insuficiente');
            }

            $session_id = \Str::uuid();
            $token = rand(100000, 999999);
            $session_data = base64_encode(serialize(['session_id' => $session_id, 'amount' => $amount]));

            Token::create([
                'client_id' => $client->id,
                'session_id' => $session_data,
                'token' => $token,
            ]);

            // Simular envío de correo
            \Log::info("Token enviado al email {$client->email}: $token y sessionId: $session_data");

            return $this->response(true, '00', 'Token generado con éxito. Revisa tu correo', ['session_id' => $session_id]);
        } catch (\Exception $e) {
            return $this->response(false, '04', 'Error al generar pago: ' . $e->getMessage());
        }
    }

    public function confirmarPago($session_id, $token)
    {
        $validator = Validator::make([
            'session_id' => $session_id,
            'token' => $token,
        ], [
            'session_id' => 'required|exists:tokens,session_id',
            'token' => 'required|exists:tokens,token',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors));
        }
        try {
            $tokenData = Token::where('session_id', $session_id)->where('token', $token)->firstOrFail();
            $wallet = $tokenData->client->wallet;
            $data = unserialize(base64_decode($session_id));

            $wallet->balance -= $data['amount'];
            $wallet->save();

            $tokenData->delete();

            return $this->response(true, '00', 'Pago confirmado con éxito');
        } catch (\Exception $e) {
            return $this->response(false, '05', 'Error al confirmar pago: ' . $e->getMessage());
        }
    }
    public function consultarSaldo($document, $phone)
    {
        $validator = Validator::make([
            'document' => $document,
            'phone' => $phone,
        ], [
            'document' => 'required|exists:clients,document',
            'phone' => 'required|exists:clients,phone',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->response(false, '01', implode(' ', $errors));
        }

        try {
            $client = Client::where('document', $document)->where('phone', $phone)->firstOrFail();
            $amount = number_format($client->wallet->balance, 0, '.', '.');
            return $this->response(true, '00', ['balance' => $amount]);
        } catch (\Exception $e) {
            return $this->response(false, '06', 'Error al consultar saldo: ' . $e->getMessage());
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