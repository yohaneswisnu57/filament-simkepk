<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Protocol;

class CertificateValidationController extends Controller
{
    public function verify(string $uuid)
    {
        // Find the protocol by UUID
        $protocol = Protocol::where('certificate_uuid', $uuid)->first();

        // Check if protocol exists and status is valid (Exempted/Certificate)
        $isValid = false;
        if ($protocol) {
            $statusName = strtolower($protocol->statusReview?->status_name ?? '');
            if (str_contains($statusName, 'exempted') || str_contains($statusName, 'certificate')) {
                $isValid = true;
            }
        }

        return view('certificates.verify', compact('protocol', 'isValid'));
    }
}
