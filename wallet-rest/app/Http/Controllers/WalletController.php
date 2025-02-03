<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use SoapClient;
use SoapFault;

class WalletController extends Controller
{
    protected $soapUrl = 'http://127.0.0.1:8000/api/wsdl'; // URL de tu servicio SOAP

    /**
     * Registra un cliente en la billetera
     */
    public function registroCliente(Request $request)
    {
        $params = $request->only(['document', 'name', 'email', 'phone']);
        return response()->json($this->callSoapMethod('registroCliente', $params));
    }

    /**
     * Recarga la billetera del usuario
     */
    public function recargarBilletera(Request $request)
    {
        $params = $request->only(['document', 'phone', 'valor']);
        return response()->json($this->callSoapMethod('recargaBilletera', $params));
    }

    /**
     * Realiza un pago con la billetera
     */
    public function pagar(Request $request)
    {
        $params = $request->only(['document', 'amount']);
        return response()->json($this->callSoapMethod('pagar', $params));
    }

    /**
     * Confirma un pago realizado con la billetera
     */
    public function confirmarPago(Request $request)
    {
        $params = $request->only(['sessionId', 'token']);
        return response()->json($this->callSoapMethod('confirmarPago', $params));
    }

    /**
     * Consulta el saldo de la billetera del usuario
     */
    public function consultarSaldo(Request $request)
    {
        $params = $request->only(['document', 'phone']);
        return response()->json($this->callSoapMethod('consultarSaldo', $params));
    }

    /**
     * Ejecuta una llamada SOAP y maneja excepciones
     */
    private function callSoapMethod(string $method, array $params)
    {
        try {
            $client = new SoapClient($this->soapUrl, [
                'trace' => 1,
                'exceptions' => true,
            ]);
            $response = $client->__soapCall($method, $params);
            return [
                'success' => isset($response["success"]) ? (bool) $response["success"] : false,
                'code' => $response["cod"] ?? "01",
                'message' => $response["message"] ?? "Error desconocido",
            ];
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'code' => "00",
                'message' => $e->getMessage(),
            ];
        }
    }
}
