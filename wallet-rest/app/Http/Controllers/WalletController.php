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

    
}
