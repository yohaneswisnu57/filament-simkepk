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

    @if(session('error'))
    <script>
        window.onload = function() {
            alert("{{ session('error') }}");
        };
    </script>
    @endif

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
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors mr-4">Tentang Kami</a>
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
                    <a href="{{ route('downloads.requirement', ['filename' => 'Data_Pemohon_KEPK.docx']) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 hover:border-primary-300 hover:text-primary-700 shadow-sm">
                        <i class="ph-duotone ph-file-doc mr-2 text-xl text-primary-600"></i>
                        Data Pemohon KEPK
                    </a>
                    <a href="{{ route('downloads.requirement', ['filename' => 'Informed_Consent_KEPK.docx']) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 hover:border-primary-300 hover:text-primary-700 shadow-sm">
                        <i class="ph-duotone ph-file-doc mr-2 text-xl text-primary-600"></i>
                        Informed Consent
                    </a>
                    <a href="{{ route('downloads.jenis-protokol') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 hover:border-primary-300 hover:text-primary-700 shadow-sm">
                        <i class="ph-duotone ph-files mr-2 text-xl text-primary-600"></i>
                        Jenis Protokol
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
    <section id="about" class="py-24 bg-white relative overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-primary-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-40 right-1/4 w-80 h-80 bg-medical-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse animation-delay-2000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20">
                <span class="text-primary-600 font-bold tracking-widest uppercase text-xs mb-3 block">Mengenal Lebih Dekat</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-6">Tentang <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-medical-600">Kami</span></h2>
                <div class="w-24 h-1.5 bg-slate-100 mx-auto rounded-full relative">
                    <div class="absolute inset-0 w-1/2 bg-primary-600 rounded-full"></div>
                </div>
            </div>
            
            <div class="space-y-32">
                @foreach($abouts as $index => $about)
                <div class="flex flex-col {{ $index % 2 == 1 ? 'md:flex-row-reverse' : 'md:flex-row' }} items-center gap-12 lg:gap-24">
                    <!-- Image Column with Decorative Frame -->
                    @if($about->image_path)
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-primary-100 to-medical-100 rounded-[2.5rem] transform {{ $index % 2 == 1 ? '-rotate-3' : 'rotate-3' }} group-hover:rotate-0 transition-all duration-700 -z-10 opacity-60"></div>
                        <div class="relative overflow-hidden rounded-[2rem] shadow-2xl border-8 border-white">
                            <img src="{{ Storage::url($about->image_path) }}" 
                                 alt="{{ $about->title }}" 
                                 class="w-full h-auto object-cover max-h-[500px] transform group-hover:scale-105 transition-transform duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <!-- Floating Badge -->
                        <div class="absolute -bottom-6 {{ $index % 2 == 1 ? '-left-6' : '-right-6' }} bg-white p-4 rounded-2xl shadow-xl border border-slate-100 hidden lg:block animate-bounce animation-duration-3000">
                           <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white">
                                    <i class="ph-bold ph-seal-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Terlisensi</p>
                                    <p class="text-sm font-extrabold text-slate-900">Komite Etik Nasional</p>
                                </div>
                           </div>
                        </div>
                    </div>
                    @endif

                    <!-- Content Column -->
                    <div class="w-full {{ $about->image_path ? 'md:w-1/2' : 'max-w-4xl mx-auto text-center' }}">
                        <div class="inline-flex items-center gap-3 mb-6 px-4 py-1.5 bg-slate-50 border border-slate-100 rounded-full">
                            <span class="w-2 h-2 bg-primary-600 rounded-full animate-ping"></span>
                            <span class="text-xs font-bold text-primary-700 uppercase tracking-wider">Visi & Misi Kami</span>
                        </div>
                        <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-8 leading-tight">{{ $about->title }}</h3>
                        
                        <div class="prose prose-lg prose-slate prose-primary max-w-none text-slate-600 leading-relaxed mb-10">
                            {!! $about->content !!}
                        </div>
                        
                        @if($about->image_path)
                        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-slate-100">
                            <div>
                                <h4 class="text-3xl font-black text-primary-600 mb-1">Online</h4>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Sistem Full Digital</p>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-medical-600 mb-1">Cepat</h4>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Proses Telaah Efisien</p>
                            </div>
                        </div>
                        @else
                        <div class="flex justify-center gap-12 pt-8 border-t border-slate-100">
                             <div class="text-center">
                                <h4 class="text-3xl font-black text-primary-600 mb-1">Integrity</h4>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Etika Utama</p>
                            </div>
                            <div class="text-center">
                                <h4 class="text-3xl font-black text-medical-600 mb-1">Trusted</h4>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Hasil Akurat</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Features / Alur Section -->
    <section id="alur" class="py-24 bg-slate-50 relative border-y border-slate-200 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Alur Pelayanan KEPK</h2>
                <p class="text-slate-500 max-w-2xl mx-auto text-lg">Proses simplifikasi dan transparan untuk penerbitan Ethical Clearance yang akuntabel.</p>
                <div class="w-20 h-1.5 bg-primary-600 mx-auto rounded-full mt-6"></div>
            </div>

            <!-- Mobile View (Timeline Vertical List) -->
            <div class="lg:hidden space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
               
                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-primary-600 text-white shadow shrink-0 z-10">
                        1
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl bg-white shadow-md shadow-slate-200/50 border border-slate-100">
                        <h4 class="font-bold text-slate-800 text-lg">Penerimaan Berkas</h4>
                        <p class="text-sm text-primary-600 font-medium mt-1">simkepk.ukwms.ac.id</p>
                    </div>
                </div>
                
                <div class="relative flex items-center justify-between md:justify-normal md:even:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-primary-600 text-white shadow shrink-0 z-10">
                        2
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl bg-white shadow-md shadow-slate-200/50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-slate-800 text-lg">Review Cepat – 3 Orang</h4>
                        </div>
                        <p class="text-sm text-slate-500 mb-3">(Ketua & Sekretaris KEPK)</p>
                        <div class="flex gap-2">
                           <span class="text-xs font-bold px-2.5 py-1.5 bg-medical-50 text-medical-700 border border-medical-200 rounded-lg">Maks 5 Hari</span>
                           <span class="inline-flex items-center gap-1 text-xs font-bold bg-green-50 text-green-700 border border-green-200 px-2.5 py-1.5 rounded-lg ml-auto">
                                <i class="ph-fill ph-arrow-right"></i> EXEMPTED
                           </span>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-primary-600 text-white shadow shrink-0 z-10">
                        3
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl bg-white shadow-md shadow-slate-200/50 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-slate-800 text-lg">Telaah Mendalam</h4>
                        </div>
                        <p class="text-sm text-slate-500 mb-3">Review Kelompok</p>
                        <div class="flex flex-wrap gap-2 mb-3">
                           <span class="text-xs font-bold px-2.5 py-1.5 bg-medical-50 text-medical-700 border border-medical-200 rounded-lg w-full text-center">14 Hari Pengerjaan</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center justify-center gap-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1.5 rounded-lg flex-1">
                                EXPEDITED
                            </span>
                            <span class="inline-flex items-center justify-center gap-1 text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1.5 rounded-lg flex-1">
                                FULL BOARD
                            </span>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:even:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-primary-600 text-white shadow shrink-0 z-10">
                        4
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl bg-white shadow-md shadow-slate-200/50 border border-slate-100">
                        <h4 class="font-bold text-slate-800 text-lg">Persetujuan</h4>
                        <p class="text-sm text-slate-500 mt-1">(+ Rekomendasi Kpd Peneliti)</p>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-green-500 text-white shadow shrink-0 shadow-green-500/50 z-10">
                        <i class="ph-bold ph-certificate"></i>
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-5 rounded-2xl bg-white shadow-xl shadow-green-500/10 ring-2 ring-green-400 border-transparent">
                        <h4 class="font-extrabold text-green-700 text-lg uppercase tracking-wide">Surat Laik Etik</h4>
                        <p class="text-sm text-slate-600 mt-1">Diterbitkan otomatis sistem.</p>
                    </div>
                </div>
            </div>

            <!-- Desktop View (Visual Branching Flowchart) -->
            <div class="hidden lg:block relative max-w-5xl mx-auto pt-6">
                <!-- CSS Connectors (Absolute) -->
                <div class="absolute inset-0 pointer-events-none z-0">
                    <!-- Vertical Spine Line connecting stages -->
                    <div class="absolute left-1/2 top-[70px] bottom-[100px] w-[3px] bg-slate-300 -translate-x-1/2"></div>
                    <!-- Spine Arrow Heads -->
                    <i class="ph-fill ph-caret-down text-2xl text-slate-400 absolute left-1/2 top-[155px] -translate-x-1/2 -ml-[0.5px]"></i>
                    <i class="ph-fill ph-caret-down text-2xl text-slate-400 absolute left-1/2 top-[325px] -translate-x-1/2 -ml-[0.5px]"></i>
                    <i class="ph-fill ph-caret-down text-2xl text-slate-400 absolute left-1/2 top-[495px] -translate-x-1/2 -ml-[0.5px]"></i>
                    <i class="ph-fill ph-caret-down text-2xl text-slate-400 absolute left-1/2 top-[665px] -translate-x-1/2 -ml-[0.5px]"></i>

                    <!-- Branch: Review Cepat to Exempted (Right) -->
                    <div class="absolute left-1/2 top-[230px] w-[31%] h-[3px] bg-slate-300"></div>
                    <i class="ph-fill ph-caret-right text-2xl text-slate-400 absolute left-[81%] top-[220px] -ml-2"></i>

                    <!-- Branch: Telaah Mendalam to Expedited (Left) -->
                    <div class="absolute right-1/2 top-[400px] w-[31%] h-[3px] bg-slate-300"></div>
                    <i class="ph-fill ph-caret-left text-2xl text-slate-400 absolute right-[81%] top-[390px] -mr-2"></i>

                    <!-- Branch: Telaah Mendalam to Full Board (Right) -->
                    <div class="absolute left-1/2 top-[400px] w-[31%] h-[3px] bg-slate-300"></div>
                    <i class="ph-fill ph-caret-right text-2xl text-slate-400 absolute left-[81%] top-[390px] -ml-2"></i>

                    <!-- Skip Branch: Exempted & Full Board returning to Surat Laik Etik -->
                    <div class="absolute right-[5%] top-[230px] bottom-[720px] w-[3px] bg-blue-300 opacity-60"></div>
                    <div class="absolute right-[5%] top-[230px] h-[3px] w-[3%] bg-blue-300 opacity-60 -translate-x-full"></div>
                    <!-- Drop down line -->
                    <div class="absolute right-[5%] top-[230px] bottom-[115px] w-[3px] bg-blue-300 opacity-60 border-l-[3px] border-dashed border-white"></div>
                    <!-- Return line -->
                    <div class="absolute right-[5%] bottom-[115px] w-[27%] h-[3px] bg-blue-300 opacity-60"></div>
                    <i class="ph-fill ph-caret-left text-2xl text-blue-400 absolute right-[32%] bottom-[105px] opacity-80 z-20"></i>
                </div>

                <!-- CSS Grid for perfect positioning -->
                <div class="grid grid-cols-3 gap-x-12 gap-y-[70px] relative z-10 w-full">
                    
                    <!-- Tahap 1 -->
                    <div class="col-start-2 flex justify-center">
                        <div class="w-[340px] bg-white border border-slate-200 rounded-2xl p-6 shadow-lg shadow-slate-200/50 text-center font-bold text-slate-800 flex flex-col items-center justify-center h-[100px]">
                            <h4 class="text-lg">PENERIMAAN BERKAS</h4>
                            <div class="text-sm text-primary-600 font-medium mt-1">(simkepk.ukwms.ac.id)</div>
                        </div>
                    </div>

                    <!-- Tahap 2 -->
                    <div class="col-start-2 flex justify-center">
                        <div class="w-[340px] bg-white border border-slate-200 rounded-2xl p-5 shadow-lg shadow-slate-200/50 text-center flex flex-col items-center justify-center h-[100px] relative z-20">
                            <h4 class="font-bold text-slate-800 text-base">REVIEW CEPAT – 3 ORANG</h4>
                            <div class="text-xs text-slate-500 font-normal mt-0.5">(Ketua – Sekretaris)</div>
                            <div class="mt-2 text-[11px] bg-medical-50 border border-medical-200 text-medical-700 font-bold px-2.5 py-1 rounded-md tracking-wide uppercase">Maks 5 Hari Kerja</div>
                        </div>
                    </div>
                    <div class="col-start-3 flex justify-center items-center">
                        <div class="w-full max-w-[240px] bg-white border border-green-200 rounded-xl p-4 shadow-md text-center h-[80px] flex items-center justify-center relative z-20 overflow-hidden group">
                            <div class="absolute left-0 top-0 bottom-0 w-2 bg-green-500"></div>
                            <h4 class="font-extrabold text-green-700 tracking-wider text-lg ml-2">EXEMPTED</h4>
                        </div>
                    </div>

                    <!-- Tahap 3 -->
                    <div class="col-start-1 flex justify-center items-center">
                        <div class="w-full max-w-[240px] bg-white border border-amber-200 rounded-xl p-4 shadow-md text-center h-[80px] flex items-center justify-center relative z-20">
                           <div class="absolute right-0 top-0 bottom-0 w-2 bg-amber-500"></div>
                           <h4 class="font-extrabold text-amber-700 tracking-wider text-lg mr-2">EXPEDITED</h4>
                        </div>
                    </div>
                    <div class="col-start-2 flex justify-center">
                        <div class="w-[340px] bg-white border border-slate-200 rounded-2xl p-5 shadow-lg shadow-slate-200/50 text-center flex flex-col items-center justify-center h-[100px] relative z-20">
                            <h4 class="font-bold text-slate-800 text-base uppercase">Telaah Mendalam</h4>
                            <div class="text-xs text-slate-500 font-normal mt-0.5 mb-2">Review Kelompok</div>
                            <div class="text-[11px] bg-medical-50 border border-medical-200 text-medical-700 font-bold px-2.5 py-1 rounded-md tracking-wide uppercase">14 Hari</div>
                        </div>
                    </div>
                    <div class="col-start-3 flex justify-center items-center">
                        <div class="w-full max-w-[240px] bg-white border border-purple-200 rounded-xl p-4 shadow-md text-center h-[80px] flex items-center justify-center relative z-20">
                            <div class="absolute left-0 top-0 bottom-0 w-2 bg-purple-500"></div>
                            <h4 class="font-extrabold text-purple-700 tracking-wider text-lg ml-2">FULL BOARD</h4>
                        </div>
                    </div>

                    <!-- Tahap 4 -->
                    <div class="col-start-2 flex justify-center">
                        <div class="w-[340px] bg-white border border-slate-200 rounded-2xl p-6 shadow-lg shadow-slate-200/50 text-center flex flex-col items-center justify-center h-[100px] relative z-20">
                            <h4 class="font-bold text-slate-800 text-lg uppercase tracking-wide">Persetujuan</h4>
                            <div class="text-sm text-slate-500 font-medium mt-1">(+ Rekomendasi Kpd Peneliti)</div>
                        </div>
                    </div>

                    <!-- Tahap 5 -->
                    <div class="col-start-2 flex justify-center relative mt-4">
                        <div class="w-[340px] bg-white border-2 border-green-500 rounded-2xl p-6 shadow-[0_10px_40px_-15px_rgba(34,197,94,0.4)] text-center relative overflow-hidden group h-[110px] flex flex-col items-center justify-center z-20">
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-green-100/40 to-transparent group-hover:animate-shimmer"></div>
                            <div class="relative z-10 flex items-center gap-3">
                                <i class="ph-fill ph-certificate text-4xl text-green-600"></i>
                                <h4 class="font-extrabold text-2xl text-green-700 tracking-wide uppercase mt-1">Surat Laik Etik</h4>
                            </div>
                        </div>
                    </div>

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

    <!-- Multi-Column Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <!-- University Branding Block -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS" class="w-16 h-16 object-contain mb-6">
                    <h3 class="text-lg font-bold text-white leading-tight tracking-tight uppercase mb-8">
                        Universitas Katolik<br>
                        Widya Mandala<br>
                        Surabaya
                    </h3>
                    
                    <!-- Social Media Block (Image Style) -->
                    <div class="flex flex-col items-center md:items-start w-full md:w-auto">
                        <div class="flex gap-6 mb-4 text-amber-500">
                            <a href="#" class="hover:text-amber-400 transition-colors"><i class="ph ph-tiktok-logo text-2xl"></i></a>
                            <a href="#" class="hover:text-amber-400 transition-colors"><i class="ph ph-instagram-logo text-2xl"></i></a>
                            <a href="#" class="hover:text-amber-400 transition-colors"><i class="ph ph-youtube-logo text-2xl"></i></a>
                            <a href="#" class="hover:text-amber-400 transition-colors"><i class="ph ph-twitter-logo text-2xl"></i></a>
                            <a href="#" class="hover:text-amber-400 transition-colors"><i class="ph ph-linkedin-logo text-2xl"></i></a>
                        </div>
                        <div class="w-full border-t border-amber-500 py-3 text-center md:text-left">
                            <a href="https://unika.widyamandala.ac.id/" target="_blank" class="text-primary-400 font-bold hover:text-primary-300 transition-colors underline decoration-amber-500 decoration-2 underline-offset-8">@UKWMS</a>
                        </div>
                        <div class="w-full border-t border-amber-500"></div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <h4 class="text-white font-bold uppercase tracking-widest text-sm mb-4">Navigasi Cepat</h4>
                    <nav class="flex flex-col gap-3 text-sm">
                        <a href="#about" class="hover:text-primary-400 transition-colors">Tentang Kami</a>
                        <a href="#alur" class="hover:text-primary-400 transition-colors">Alur Pengajuan</a>
                        <a href="#faq" class="hover:text-primary-400 transition-colors">Pertanyaan Umum</a>
                        <a href="{{ url('/user') }}" class="text-primary-500 font-semibold hover:text-primary-400 mt-2">Portal Peneliti</a>
                        <a href="{{ url('/reviewer') }}" class="text-reviewer-500 font-semibold hover:text-reviewer-400">Portal Reviewer</a>
                    </nav>
                </div>

                <!-- Contact Info -->
                <div class="flex flex-col items-center md:items-start gap-4">
                    <h4 class="text-white font-bold uppercase tracking-widest text-sm mb-4">Hubungi Kami</h4>
                    <div class="flex items-start gap-3 text-sm">
                        <i class="ph-duotone ph-map-pin text-xl text-primary-500"></i>
                        <p>Universitas Katolik Widya Mandala <br>
                            Tower Barat Lt. 6, <br>
                            Jl. Raya Kalisari Selatan No.1, Pakuwon City, Surabaya</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ph-duotone ph-phone text-xl text-primary-500"></i>
                        <p>(031) 99005299 ext.10656,<br>
                        Fax.(031) 99005278,</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="ph-duotone ph-envelope text-xl text-primary-500"></i>
                        <p> kepk.fkukwms@gmail.com<br>
                        kepk.fk@ukwms.ac.id</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright bar -->
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-white text-lg tracking-tight">SIM KEPK</span>
                    <span class="text-slate-600">|</span>
                    <p class="text-sm text-slate-500 italic">Trusted Ethical Clearance System</p>
                </div>
                <div class="text-xs text-slate-500 text-center md:text-right">
                    &copy; 2026 Komite Etik Penelitian Kesehatan UKWMS. All rights reserved.
                </div>
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
