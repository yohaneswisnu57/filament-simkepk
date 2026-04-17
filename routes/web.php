<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $faqs = \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get();
    $abouts = \App\Models\About::where('is_active', true)->orderBy('sort_order')->get();
    
    return view('welcome', compact('faqs', 'abouts'));
});

Route::get('/certificates/protocol/{protocol}', [CertificateController::class, 'show'])
    ->name('certificates.protocol');

Route::get('/verify/{uuid}', [CertificateValidationController::class, 'verify'])
    ->name('certificates.verify');

Route::get('/downloads/requirement/{filename}', function ($filename) {
    // Mencegah directory traversal attack
    if (str_contains($filename, '..') || str_contains($filename, '/')) {
        abort(403);
    }
    
    $path = storage_path('app/private/download_proposal_formconcern/' . $filename);
    
    if (!file_exists($path)) {
        abort(404, 'Dokumen persyaratan tidak ditemukan.');
    }
    
    return response()->download($path);
})->name('downloads.requirement');
