<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('registro-cliente', 'WalletController@registroCliente');
$router->post('recargar-billetera', 'WalletController@recargarBilletera');
$router->post('pagar', 'WalletController@pagar');
$router->post('confirmar-pago', 'WalletController@confirmarPago');
$router->post('consultar-saldo', 'WalletController@consultarSaldo');