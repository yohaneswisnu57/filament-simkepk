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

        // Pastikan status protokol adalah Exempted atau Certificate
        $statusName = strtolower($protocol->statusReview?->status_name ?? '');
        $isAllowedStatus = str_contains($statusName, 'exempted') || str_contains($statusName, 'certificate');

        if (! $isAllowedStatus) {
            throw new AccessDeniedHttpException('Sertifikat hanya tersedia untuk protokol dengan status Exempted atau Certificate.');
        }

        // Hanya pemilik protokol atau admin/super_admin yang boleh akses
        $canAccess = $user->id === $protocol->user_id
            || $user->hasRole(['admin', 'super_admin']);

        if (! $canAccess) {
            throw new AccessDeniedHttpException('Anda tidak memiliki akses ke sertifikat ini.');
        }

        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        
        // Mengambil nama langsung dari database. 
        // Peneliti tidak akan bisa memanipulasi nama lewat URL
        $nama_lengkap = $protocol->certificate_name ?? $user->name;

        // Generate UUID untuk tracker jika belum ada
        if (! $protocol->certificate_uuid) {
            $protocol->updateQuietly([
                'certificate_uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'certificate_published_at' => now(),
            ]);
        }

        return view('certificates.protocol', compact('protocol', 'nama_lengkap'));
    }
}
