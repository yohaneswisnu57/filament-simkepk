<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - SIMKEPK UKWMS</title>

    <meta name="description" content="Kebijakan privasi SIMKEPK UKWMS mengenai pengumpulan, penggunaan, dan perlindungan data pengguna, termasuk data yang diperoleh melalui login Google.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://simkepk.ukwms.ac.id/privacy-policy">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <nav class="border-b border-slate-200 bg-white">
        <div class="max-w-3xl mx-auto px-6 py-4 flex items-center gap-3">
            <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS" class="w-8 h-8 object-contain">
            <a href="{{ url('/') }}" class="font-semibold text-slate-900">SIMKEPK UKWMS</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Kebijakan Privasi</h1>
        <p class="text-sm text-slate-500 mb-10">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="prose prose-slate max-w-none space-y-8">
            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">1. Pendahuluan</h2>
                <p>SIMKEPK UKWMS ("kami") adalah platform Sistem Informasi Manajemen Komite Etik Penelitian milik Universitas Katolik Widya Mandala Surabaya. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pengguna, termasuk data yang diperoleh saat Anda masuk menggunakan akun Google.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">2. Data yang Kami Kumpulkan</h2>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Nama lengkap</li>
                    <li>Alamat email</li>
                    <li>Identifier akun Google (saat Anda memilih login menggunakan Google)</li>
                    <li>Data terkait pengajuan dan telaah etik penelitian yang Anda masukkan ke dalam sistem</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">3. Penggunaan Data</h2>
                <p>Data yang dikumpulkan digunakan semata-mata untuk keperluan autentikasi, pengelolaan akun, serta proses pengajuan dan telaah etik penelitian di dalam platform SIMKEPK UKWMS. Kami tidak membagikan data pribadi Anda kepada pihak ketiga di luar keperluan operasional platform ini.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">4. Login dengan Google</h2>
                <p>Saat Anda memilih login menggunakan Google, kami hanya menerima informasi dasar profil (nama, email, dan identifier akun) yang diizinkan oleh Google sesuai izin yang Anda berikan. Kami tidak mengakses atau menyimpan kata sandi akun Google Anda.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">5. Keamanan Data</h2>
                <p>Kami menerapkan langkah-langkah teknis yang wajar untuk melindungi data Anda dari akses, perubahan, atau pengungkapan yang tidak sah.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-slate-900 mb-2">6. Kontak</h2>
                <p>Jika Anda memiliki pertanyaan mengenai kebijakan privasi ini, silakan hubungi Komite Etik Penelitian Universitas Katolik Widya Mandala Surabaya.</p>
            </section>
        </div>

        <a href="{{ url('/') }}" class="inline-block mt-12 text-primary-600 hover:text-primary-700 font-medium">&larr; Kembali ke beranda</a>
    </main>
</body>
</html>
