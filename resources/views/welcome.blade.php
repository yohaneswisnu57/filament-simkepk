<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM KEPK - Welcome</title>

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
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white">
                        <i class="ph-bold ph-first-aid text-xl"></i>
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
                <a href="#panduan" class="flex items-center justify-between text-lg font-semibold text-slate-700 p-4 bg-slate-50 rounded-xl">
                    Panduan <i class="ph ph-caret-right"></i>
                </a>
                <a href="#faq" class="flex items-center justify-between text-lg font-semibold text-slate-700 p-4 bg-slate-50 rounded-xl">
                    FAQ <i class="ph ph-caret-right"></i>
                </a>
                
                <a href="http://simkepk.ukwms.ac.id/admin" class="mt-4 text-center text-white bg-primary-600 px-6 py-4 rounded-2xl font-bold shadow-lg shadow-primary-500/30">
                    Login Peneliti
                </a>
            </nav>
        </div>
    </div>

    
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                        <i class="ph-bold ph-first-aid text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-xl text-slate-900 leading-none">SIM KEPK</h1>
                        <p class="text-[10px] font-medium text-slate-500 tracking-wider uppercase mt-0.5">Komite Etik Penelitian Kesehatan</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#alur" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Alur Pengajuan</a>
                    <a href="http://simkepk.ukwms.ac.id/admin" class="text-sm font-semibold text-primary-600 hover:text-primary-700 px-4 py-2 rounded-full hover:bg-primary-50 transition-colors">
                        Login Peneliti
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

                <!-- Tombol Upload / Admin -->
                <button onclick="{{ redirect('admin') }}" class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-xl bg-primary-600 px-8 py-3 font-semibold text-white transition-all duration-300 hover:bg-primary-700 hover:scale-105 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2">
                    <span class="mr-2 text-lg">Masuk / Upload Protokol</span>
                    <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:animate-shimmer"></div>
                </button>

                <!-- Tombol Sekunder -->
                <button class="inline-flex h-14 items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-8 py-3 font-semibold text-slate-700 transition-colors hover:border-primary-200 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:ring-offset-2">
                    <i class="ph ph-magnifying-glass mr-2 text-lg"></i>
                    Cek Status
                </button>
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
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary-600 rounded flex items-center justify-center text-white">
                    <i class="ph-bold ph-first-aid text-lg"></i>
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

    <!-- Script for subtle interaction -->
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
    </script>
</body>
</html>
