<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateValidationController;
use App\Http\Controllers\RequirementDownloadController;
use App\Models\About;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
    $abouts = About::where('is_active', true)->orderBy('sort_order')->get();

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

    $path = storage_path('app/private/download_proposal_formconcern/'.$filename);

    if (! file_exists($path)) {
        abort(404, 'Dokumen persyaratan tidak ditemukan.');
    }

    return response()->download($path);
})->name('downloads.requirement');

Route::get('/downloads/jenis-protokol', [RequirementDownloadController::class, 'downloadJenisProtokol'])
    ->name('downloads.jenis-protokol');

Route::get('/downloads/import-reviewer-template', function () {
    $path = storage_path('app/private/templates/import_reviewer_template.csv');

    if (! file_exists($path)) {
        abort(404, 'Template tidak ditemukan.');
    }

    return response()->download($path);
})->middleware('auth')->name('downloads.import-reviewer-template');
Route::get('/leave-impersonation', function () {
    if (! session()->has('impersonated_by')) {
        return redirect('/');
    }

    $originalUserId = session()->get('impersonated_by');
    $originalUser = User::find($originalUserId);

    if ($originalUser) {
        auth()->login($originalUser);
        session()->forget('impersonated_by');
        session()->put([
            'password_hash_'.auth()->getDefaultDriver() => $originalUser->getAuthPassword(),
        ]);
        session()->regenerate();
        session()->save();

        if ($originalUser->hasRole(['super_admin', 'admin'])) {
            return redirect('/admin');
        } elseif ($originalUser->hasRole(['panel_reviewer', 'reviewer', 'Ketua', 'sekertaris'])) {
            return redirect('/reviewer');
        } else {
            return redirect('/user');
        }
    }

    return redirect('/');
})->middleware('auth')->name('leave-impersonation');
