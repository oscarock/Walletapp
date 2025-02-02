<?php

namespace App\Http\Controllers;

use App\Services\Soap\WalletSoapServer;
use SoapServer;

class SoapController extends Controller
{
    public function handle()
    {
        $wsdl = storage_path('wsdl/wallet.wsdl');
        $server = new SoapServer($wsdl, [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace'      => 1
        ]);

        $server->setClass(WalletSoapServer::class);

        ob_start();
        $server->handle();
        $response = ob_get_clean();

        return response()->make($response, 200, ['Content-Type' => 'text/xml']);
    }
}
