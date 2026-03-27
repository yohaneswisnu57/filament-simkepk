<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Protocol;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CertificateController extends Controller
{
    public function show(Protocol $protocol, Request $request): mixed
    {
        // Filament tidak mendaftarkan route 'login' — redirect manual ke Filament admin login
        if (! auth()->check()) {
            return redirect('/admin/login');
        }

        $user = auth()->user();

        // Pastikan status protokol adalah Exempted
        $isExempted = str_contains(
            strtolower($protocol->statusReview?->status_name ?? ''),
            'exempted'
        );

        if (! $isExempted) {
            throw new AccessDeniedHttpException('Sertifikat hanya tersedia untuk protokol dengan status Exempted.');
        }

        // Hanya pemilik protokol atau admin/super_admin yang boleh akses
        $canAccess = $user->id === $protocol->user_id
            || $user->hasRole(['admin', 'super_admin']);

        if (! $canAccess) {
            throw new AccessDeniedHttpException('Anda tidak memiliki akses ke sertifikat ini.');
        }

        $nama_lengkap = $request->query('nama', $user->name);

        return view('certificates.protocol', compact('protocol', 'nama_lengkap'));
    }
}
