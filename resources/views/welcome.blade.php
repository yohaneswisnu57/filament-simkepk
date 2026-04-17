<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM KEPK - Welcome</title>

    <meta name="title" content="SIMKEPK UKWMS - Sistem Informasi Manajemen Komite Etik Penelitian">
    <meta name="description" content="SIMKEPK UKWMS (Sistem Informasi Manajemen Komite Etik Penelitian) adalah platform resmi Universitas Katolik Widya Mandala Surabaya untuk pengajuan dan telaah etik penelitian secara online, mudah, dan transparan.">
    <meta name="keywords" content="SIMKEPK UKWMS, KEPK UKWMS, Komite Etik Penelitian Surabaya, Etik Penelitian Widya Mandala, Pengajuan Etik Online, Ethical Clearance UKWMS, SIMKEP Universitas Katolik Widya Mandala">
    <meta name="author" content="Universitas Katolik Widya Mandala Surabaya">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">
    
    <!-- Canonical URL (Ganti dengan URL asli website saat rilis) -->
    <link rel="canonical" href="https://simkepk.ukwms.ac.id/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://simkepk.ukwms.ac.id/">
    <meta property="og:title" content="SIMKEPK UKWMS - Sistem Informasi Manajemen Komite Etik Penelitian">
    <meta property="og:description" content="Platform resmi Universitas Katolik Widya Mandala Surabaya untuk pengajuan dan telaah etik penelitian secara online, mudah, dan transparan.">
    <meta property="og:image" content="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://simkepk.ukwms.ac.id/">
    <meta property="twitter:title" content="SIMKEP UKWMS - Sistem Informasi Manajemen Komite Etik Penelitian">
    <meta property="twitter:description" content="Platform resmi Universitas Katolik Widya Mandala Surabaya untuk pengajuan dan telaah etik penelitian secara online, mudah, dan transparan.">
    <meta property="twitter:image" content="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons: Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

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
                            600: '#2563eb', // Royal Blue
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        medical: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            600: '#0d9488', // Teal
                        },
                        reviewer: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            600: '#d97706', // Amber/Orange
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Gradient Animation */
        .blob {
            position: absolute;
            filter: blur(40px);
            z-index: -1;
            opacity: 0.4;
            animation: move 10s infinite alternate;
        }
        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(20px, -20px) scale(1.1); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden selection:bg-primary-100 selection:text-primary-900">

    <input type="checkbox" id="menu-toggle" class="peer hidden" />

    <!-- Mobile Navigation -->
    <label for="menu-toggle" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden peer-checked:block md:hidden transition-all"></label>

    <div class="fixed inset-x-0 top-0 z-[60] bg-white shadow-2xl transform -translate-y-full peer-checked:translate-y-0 transition-transform duration-300 ease-in-out md:hidden rounded-b-3xl">
        <div class="flex flex-col p-6">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center">
                        <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS" class="w-8 h-8 object-contain">
                    </div>
                    <span class="font-bold text-slate-900">Menu Navigasi</span>
                </div>
                <label for="menu-toggle" class="w-10 h-10 flex items-center justify-center bg-slate-100 rounded-full text-slate-600 cursor-pointer">
                    <i class="ph ph-x text-2xl"></i>
                </label>
            </div>

            <nav class="flex flex-col gap-4">
                <a href="#alur" class="flex items-center justify-between text-lg font-semibold text-slate-700 p-4 bg-slate-50 rounded-xl">
                    Alur Pengajuan <i class="ph ph-caret-right"></i>
                </a>


                <a href="{{ url('/user') }}" class="mt-4 text-center text-white bg-primary-600 px-6 py-4 rounded-2xl font-bold shadow-lg shadow-primary-500/30">
                    Login Peneliti
                </a>
                <a href="{{ url('/reviewer') }}" class="mt-2 text-center text-white bg-reviewer-600 px-6 py-4 rounded-2xl font-bold shadow-lg shadow-reviewer-500/30">
                    Login Reviewer
                </a>
            </nav>
        </div>
    </div>


    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="flex items-center justify-center">
                        <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS" class="w-10 h-10 object-contain">
                    </div>
                    <div>
                        <h1 class="font-bold text-xl text-slate-900 leading-none">SIM KEPK</h1>
                        <p class="text-[10px] font-medium text-slate-500 tracking-wider uppercase mt-0.5">Komite Etik Penelitian</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <a href="#alur" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors mr-4">Alur Pengajuan</a>
                    <a href="{{ url('/user') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 px-4 py-2 rounded-full hover:bg-primary-50 transition-colors border border-primary-200">
                        Login Peneliti
                    </a>
                    <a href="{{ url('/reviewer') }}" class="text-sm font-semibold text-reviewer-600 hover:text-reviewer-700 px-4 py-2 rounded-full hover:bg-reviewer-50 transition-colors border border-reviewer-200">
                        Login Reviewer
                    </a>
                </div>

                <label for="menu-toggle" class="md:hidden flex w-12 h-12 items-center justify-center text-slate-600 hover:text-primary-600 cursor-pointer transition-colors">
                    <i class="ph ph-list text-4xl"></i>
                </label>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Blobs -->
        <div class="blob bg-blue-300 w-96 h-96 rounded-full top-0 -left-20 mix-blend-multiply"></div>
        <div class="blob bg-teal-300 w-96 h-96 rounded-full top-0 -right-20 mix-blend-multiply animation-delay-2000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">

            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                Etik Penelitian <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-medical-600">
                    Cepat & Transparan
                </span>
            </h1>

            <p class="mt-4 text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Platform terintegrasi untuk pengajuan kelaikan etik, penelaahan protokol, dan penerbitan <i>Ethical Clearance</i> secara digital.
            </p>

            <!-- Main CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">

                <!-- Tombol Cek Status (Scanner) -->
                <button onclick="openScanner()" class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-xl bg-primary-600 px-8 py-3 font-semibold text-white transition-all duration-300 hover:bg-primary-700 hover:scale-105 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2">
                    <i class="ph ph-qr-code mr-2 text-2xl"></i>
                    <span class="mr-2 text-lg">Scan QR Cek Status</span>
                    <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:animate-shimmer"></div>
                </button>
            </div>

            <!-- Download Requirements Area -->
            <div class="mt-8">
                <p class="text-sm text-slate-500 font-medium mb-3 uppercase tracking-wider">Unduh Dokumen Persyaratan Pengajuan</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                    <a href="{{ route('downloads.requirement', 'Data_Pemohon_KEPK.docx') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 hover:border-primary-300 hover:text-primary-700 shadow-sm">
                        <i class="ph-duotone ph-file-doc mr-2 text-xl text-primary-600"></i>
                        Data Pemohon KEPK
                    </a>
                    <a href="{{ route('downloads.requirement', 'Informed_Consent_KEPK.docx') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 hover:border-primary-300 hover:text-primary-700 shadow-sm">
                        <i class="ph-duotone ph-file-doc mr-2 text-xl text-primary-600"></i>
                        Informed Consent
                    </a>
                </div>
            </div>

            <!-- Stats / Trust Indicators -->
            <div class="mt-16 pt-8 border-t border-slate-200/60 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="flex flex-col items-center md:items-start">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 transition-transform hover:scale-110">
                        <i class="ph-duotone ph-file-text text-2xl"></i>
                    </div>

                    <p class="text-sm text-slate-500 font-medium">Protokol Masuk</p>
                </div>

                <div class="flex flex-col items-center md:items-start">
                    <div class="w-12 h-12 bg-medical-50 text-medical-600 rounded-xl flex items-center justify-center mb-4 transition-transform hover:scale-110">
                        <i class="ph-duotone ph-certificate text-2xl"></i>
                    </div>

                    <p class="text-sm text-slate-500 font-medium">Sertifikat Terbit</p>
                </div>

                <div class="flex flex-col items-center md:items-start">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-4 transition-transform hover:scale-110">
                        <i class="ph-duotone ph-clock-counter-clockwise text-2xl"></i>
                    </div>

                    <p class="text-sm text-slate-500 font-medium">Layanan Sistem</p>
                </div>

                <div class="flex flex-col items-center md:items-start">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4 transition-transform hover:scale-110">
                        <i class="ph-duotone ph-leaf text-2xl"></i>
                    </div>

                    <p class="text-sm text-slate-500 font-medium">Paperless</p>
                </div>
            </div>
    </section>

    @if(isset($abouts) && count($abouts) > 0)
    <!-- About Section -->
    <section id="about" class="py-20 bg-slate-50 relative border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Tentang Kami</h2>
                <div class="w-20 h-1.5 bg-primary-600 mx-auto rounded-full"></div>
            </div>
            
            <div class="flex flex-col gap-16">
                @foreach($abouts as $index => $about)
                <div class="flex flex-col {{ $index % 2 == 1 ? 'md:flex-row-reverse' : 'md:flex-row' }} gap-10 items-center">
                    @if($about->image_path)
                    <div class="w-full md:w-1/2">
                        <img src="{{ Storage::url($about->image_path) }}" alt="{{ $about->title }}" class="rounded-2xl shadow-lg w-full h-auto object-cover max-h-[400px]">
                    </div>
                    @endif
                    <div class="w-full {{ $about->image_path ? 'md:w-1/2' : '' }}">
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">{{ $about->title }}</h3>
                        <div class="prose prose-slate prose-primary max-w-none text-slate-600 leading-relaxed text-lg">
                            {!! $about->content !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Features / Alur Section -->
    <section id="alur" class="py-20 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Alur Pengajuan Mudah</h2>
                <p class="text-slate-500 max-w-xl mx-auto">Proses simplifikasi untuk mempercepat penelitian Anda tanpa mengurangi standar etik.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition-all hover:-translate-y-2 hover:shadow-lg border border-slate-100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                        <i class="ph-duotone ph-upload-simple text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">1. Upload Dokumen</h3>
                    <p class="text-slate-500 leading-relaxed">Login ke akun peneliti, isi formulir pengajuan, dan unggah dokumen protokol penelitian Anda secara digital.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition-all hover:-translate-y-2 hover:shadow-lg border border-slate-100">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-6">
                        <i class="ph-duotone ph-magnifying-glass-plus text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">2. Telaah Reviewer</h3>
                    <p class="text-slate-500 leading-relaxed">Tim penelaah melakukan review protokol secara online. Anda dapat memantau revisi dan catatan secara real-time.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition-all hover:-translate-y-2 hover:shadow-lg border border-slate-100">
                    <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600 mb-6">
                        <i class="ph-duotone ph-certificate text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">3. Penerbitan EC</h3>
                    <p class="text-slate-500 leading-relaxed">Setelah disetujui, sertifikat <i>Ethical Clearance</i> diterbitkan secara digital dengan QR Code validasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    @if(isset($faqs) && count($faqs) > 0)
    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white relative border-t border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Pertanyaan Umum (FAQ)</h2>
                <p class="text-lg text-slate-500">Temukan jawaban atas pertanyaan seputar SIM KEPK</p>
                <div class="w-20 h-1.5 bg-primary-600 mx-auto rounded-full mt-4"></div>
            </div>
            
            <div class="flex flex-col gap-4">
                @foreach($faqs as $faq)
                <details class="group bg-slate-50 border border-slate-200 rounded-xl overflow-hidden transition-all hover:border-primary-300">
                    <summary class="flex justify-between items-center font-bold cursor-pointer text-slate-800 p-6 hover:bg-slate-100 transition-colors text-lg list-none" style="list-style: none;">
                        <span>{{ $faq->question }}</span>
                        <span class="transition-transform duration-300 group-open:rotate-180 bg-white rounded-full p-2 border border-slate-200 shadow-sm text-primary-600">
                            <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </summary>
                    <div class="bg-white px-6 pb-6 pt-2 text-slate-600 prose prose-slate max-w-none leading-relaxed border-t border-slate-100">
                        {!! $faq->answer !!}
                    </div>
                </details>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Simple Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center">
                    <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS" class="w-8 h-8 object-contain">
                </div>
                <span class="font-bold text-white text-lg">SIM KEPK</span>
            </div>
            <div class="text-sm text-slate-400">
                &copy; 2026 Komite Etik Penelitian Kesehatan. All rights reserved.
            </div>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition">Privacy</a>
                <a href="#" class="hover:text-white transition">Terms</a>
                <a href="#" class="hover:text-white transition">Contact</a>
            </div>
        </div>
    </footer>

    <!-- Script for subtle interaction & QR -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Simple animation for the shimmer effect on button hover
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shimmer {
                100% {
                    transform: translateX(100%);
                }
            }
            .animate-shimmer {
                animation: shimmer 1.5s infinite;
            }
        `;
        document.head.appendChild(style);

        // QR Scanner Logic
        let html5QrcodeScanner;
        
        function openScanner() {
            document.getElementById('qr-modal').classList.remove('hidden');
            document.getElementById('qr-modal').classList.add('flex');
            
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: {width: 250, height: 250} }
            );
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function closeScanner() {
            if(html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            document.getElementById('qr-modal').classList.add('hidden');
            document.getElementById('qr-modal').classList.remove('flex');
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanner & redirect
            closeScanner();
            // Assuming the QR code is the full URL to /verify/uuid
            if (decodedText.startsWith('http')) {
                window.location.href = decodedText;
            } else {
                alert("QR Code tidak valid atau bukan referensi SIMKEPK: " + decodedText);
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
        }
    </script>

    <!-- QR Modal -->
    <div id="qr-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-bold text-lg text-slate-800">Scan QR Sertifikat</h3>
                <button onclick="closeScanner()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>
            <div class="p-4 bg-slate-50">
                <div id="reader" width="600px"></div>
                <p class="text-center text-sm text-slate-500 mt-4">Arahkan kamera ke QR Code yang tertera pada sertifikat untuk memverifikasi keasliannya.</p>
            </div>
        </div>
    </div>
</body>
</html>
