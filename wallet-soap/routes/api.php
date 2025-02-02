<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/soap-wallet', [SoapController::class, 'handle']);

Route::get('/wsdl', function () {
    return response()->file(storage_path('wsdl/wallet.wsdl'));
});