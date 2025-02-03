<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use SoapClient;
use SoapFault;

class WalletController extends Controller
{
    protected $soapUrl = 'http://127.0.0.1:8000/api/wsdl'; // URL de tu servicio SOAP

    // Registra un cliente
    public function registroCliente(Request $request)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);

            // Llamada al método SOAP
            $response = $client->__soapCall('registroCliente', [
                'document' => $request->input('document'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone')
            ]);

            //$functions = $client->__getFunctions();
            //dump($response);
            //die;
            return response()->json([
                'success' => $response["success"],
                'cod' => $response["cod"],
                'error' => $response["message"]
            ]);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function recargarBilletera(Request $request)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);

            $response = $client->__soapCall('recargaBilletera', [
                'document' => $request->input('document'),
                'phone' => $request->input('phone'),
                'valor' => $request->input('valor')
            ]);

            return response()->json([
                'code' => is_null($response["code"]) ? "01" : "00",
                'message' => $response["message"]
            ]);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function pagar(Request $request)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);

            $response = $client->__soapCall('pagar', [
                'document' => $request->input('document'),
                'amount' => $request->input('amount')
            ]);

            return response()->json([
                'code' => is_null($response["cod"]) ? "01" : "00",
                'message' => $response["message"]
            ]);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function confirmarPago(Request $request)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);

            $response = $client->__soapCall('confirmarPago', [
                'sessionId' => $request->input('sessionId'),
                'token' => $request->input('token')
            ]);

            return response()->json([
                'code' => $response["cod"],
                'message' => $response["message"]
            ]);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function consultarSaldo(Request $request)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);

            $response = $client->__soapCall('consultarSaldo', [
                'document' => $request->input('document'),
                'phone' => $request->input('phone')
            ]);

            return response()->json([
                'code' => $response["cod"],
                'message' => $response["message"]
            ]);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
