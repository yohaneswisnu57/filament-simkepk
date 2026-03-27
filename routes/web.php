<?php

use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/certificates/protocol/{protocol}', [CertificateController::class, 'show'])
    ->name('certificates.protocol');
