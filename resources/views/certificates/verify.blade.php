<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Sertifikat | SIMKEPK</title>
    <!-- Gunakan TailwindCSS via CDN khusus untuk public page ini agar estetika bagus tanpa perlu setup di luar Filament -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 font-sans">
    
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        
        <!-- Header -->
        <div class="bg-green-700 p-6 text-center text-white">
            <svg class="w-12 h-12 mx-auto mb-3 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <h1 class="text-2xl font-bold tracking-wide">SIMKEPK Tracker</h1>
            <p class="text-green-100 text-sm mt-1">Sistem Informasi Manajemen Komite Etik Penelitian</p>
        </div>

        <div class="p-6">
            @if($isValid && $protocol)
                <!-- Valid Status -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Sertifikat Valid</h2>
                    <p class="text-sm text-gray-500 mt-1">Dokumen ini resmi dan terdaftar dalam sistem SIMKEPK.</p>
                </div>

                <!-- Detail Data -->
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 space-y-4">
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Diberikan Kepada</p>
                        <p class="font-bold text-gray-900 text-lg">{{ $protocol->certificate_name ?? $protocol->user->name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Judul Protokol</p>
                        <p class="text-sm text-gray-800 leading-relaxed">{{ $protocol->perihal_pengajuan }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jenis</p>
                            <p class="text-sm font-medium text-gray-900">{{ $protocol->jenis_protocol ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Terbit</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $protocol->certificate_published_at ? \Carbon\Carbon::parse($protocol->certificate_published_at)->translatedFormat('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Kaji Etik</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $protocol->statusReview?->status_name ?? 'Exempted' }}
                        </span>
                    </div>

                </div>

            @else
                <!-- Invalid Status -->
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Tidak Valid</h2>
                    <p class="text-sm text-gray-500 mt-2">Sertifikat dengan kode ini tidak ditemukan dalam sistem kami atau telah dicabut statusnya.</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} SIMKEPK - Universitas Katolik Widya Mandala Surabaya</p>
        </div>

    </div>

</body>
</html>
