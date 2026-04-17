<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $protocol->perihal_pengajuan }}</title>
    <style>
        /* A4 Size Print Styling */
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }
        
        .certificate-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
            background: #fff;
        }

        /* ─── Header / Kop Surat ─────────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-logo {
            width: 110px;
            flex-shrink: 0;
            text-align: center;
        }

        .header-logo img {
            width: 100%;
            height: auto;
        }

        .header-text {
            flex-grow: 1;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #000;
        }

        .header-text h1, .header-text h2, .header-text h3, .header-text h4 {
            margin: 0;
            line-height: 1.25;
        }

        .header-text h1 { font-size: 13pt; font-weight: normal; }
        .header-text h2 { font-size: 14pt; font-weight: bold; }
        .header-text h3 { font-size: 14pt; font-weight: bold; }
        .header-text h4 { font-size: 16pt; font-weight: bold; margin: 3px 0 5px 0; }
        
        .header-text p {
            margin: 0;
            font-size: 10pt;
            line-height: 1.3;
        }

        .header-text a {
            color: #0066cc;
            text-decoration: none;
        }

        /* ─── Title & Reference ─────────────────────────────────────────── */
        .approval-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .approval-title h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }

        .approval-title p {
            font-size: 12pt;
            margin: 0;
        }

        /* ─── Details Table ─────────────────────────────────────────────── */
        .details-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 11pt;
            border-collapse: collapse;
        }

        .details-table td {
            vertical-align: top;
            padding: 7px 0;
        }

        .details-table td:nth-child(1) {
            width: 200px;
        }

        .details-table td:nth-child(2) {
            width: 25px;
            text-align: center;
        }

        .details-table td:nth-child(3) {
            text-align: left;
        }

        .validity-note {
            display: block;
            margin-top: 10px;
        }

        /* ─── Body Text ─────────────────────────────────────────────────── */
        .body-text {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 25px;
        }

        /* ─── Bottom Section (QR & Signature) ───────────────────────────── */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
            padding-right: 20px;
        }

        .qr-section {
            width: 40%;
            text-align: left;
        }

        .qr-code-wrapper {
            display: inline-block;
            border: 1px solid #ddd;
            padding: 8px;
            background: #fff;
        }

        .qr-label {
            font-size: 8pt;
            color: #555;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .sign-section {
            width: 45%;
            text-align: left;
        }

        .sign-section p {
            margin: 0;
            font-size: 11pt;
        }

        .sign-space {
            height: 70px;
        }

        .sign-name {
            font-weight: normal;
        }

        .sign-role {
            font-weight: bold;
        }

        /* ─── Floating Print Button ─────────────────────────────────────── */
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #15803d;
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(21,128,61,0.4);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .print-btn:hover { background: #166534; }
        .print-btn svg { width: 18px; height: 18px; fill: currentColor; }
        
        @media print {
            .print-btn, .no-print { display: none !important; }
            body { background: transparent; padding: 0; }
            .certificate-container { box-shadow: none; max-width: 100%; border: none; }
        }

        @media screen {
            body {
                background: #f5f5f0;
                padding: 40px 20px;
            }
            .certificate-container {
                padding: 50px 60px;
                box-shadow: 0 8px 40px rgba(0,0,0,0.1);
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

@php
    $publishedDate = $protocol->certificate_published_at ? \Carbon\Carbon::parse($protocol->certificate_published_at) : now();
    $romans = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
    $monthRoman = $romans[(int)$publishedDate->format('n')];
    $year = $publishedDate->format('Y');
    // Format id dengan padding nol
    $refId = str_pad($protocol->id, 5, '0', STR_PAD_LEFT);
@endphp

<div class="certificate-container">

    {{-- KOP SURAT --}}
    <div class="header">
        <div class="header-logo">
            <!-- Asumsi logo unika diambil dari external, sesuaikan path jika ada lokal -->
            <img src="https://unika.widyamandala.ac.id/wp-content/uploads/2025/05/cropped-logos.png" alt="Logo UKWMS">
        </div>
        <div class="header-text">
            <h1>YAYASAN WIDYA MANDALA SURABAYA</h1>
            <h2>UNIVERSITAS KATOLIK WIDYA MANDALA SURABAYA</h2>
            <h3>FAKULTAS KEDOKTERAN</h3>
            <h4>KOMISI ETIK PENELITIAN</h4>
            <p>Universitas Katolik Widya Mandala Tower Barat Lt. 6, Jl. Raya Kalisari Selatan No.1, Pakuwon City, Surabaya</p>
            <p>Telp.(031) 99005299 ext.10656, Fax.(031) 99005278, <a href="http://fk.ukwms.ac.id">http://fk.ukwms.ac.id</a></p>
            <p>email: <a href="mailto:kepk.fkukwms@gmail.com">kepk.fkukwms@gmail.com</a>; <a href="mailto:kepk.fk@ukwms.ac.id">kepk.fk@ukwms.ac.id</a></p>
        </div>
    </div>

    {{-- JUDUL SERTIFIKAT --}}
    <div class="approval-title">
        <h2>HEALTH RESEARCH ETHICS COMMITTEE APPROVAL</h2>
        <p>Ref: {{ $refId }}/WM12/KEPK-FK/ {{ $monthRoman }} /{{ $year }}</p>
    </div>

    {{-- TABEL DATA PROTOKOL --}}
    <table class="details-table">
        <tr>
            <td>Title of the Research<br>Protocol</td>
            <td>:</td>
            <td>{{ $protocol->perihal_pengajuan }}</td>
        </tr>
        <tr>
            <td>Principle Investigator</td>
            <td>:</td>
            <td>{{ $nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Member of Investigator</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Date of approval</td>
            <td>:</td>
            <td>
                {{ $publishedDate->translatedFormat('d F Y') }}
                <span class="validity-note">(Valid for one year beginning from the date of approval)</span>
            </td>
        </tr>
        <tr>
            <td>Institution(s)/Place(s)<br>of research</td>
            <td>:</td>
            <td>-</td>
        </tr>
    </table>

    {{-- PARAGRAF PERNYATAAN --}}
    <div class="body-text">
        <p>The Health Research Ethics Committee (HREC) states that the above protocol meets the ethical principles outlined in CIOMS and WHO (2016) International Ethical Guidelines for Health-related Research Involving Humans, CIOMS Geneva, and therefore can be carried out.</p>
        <br>
        <p>The investigator(s) is/are obliged to submit progress report, report of any serious adverse event(s) and a final report upon completion of the research. The Health Research Ethics Committee reserves the right to monitor the research activities at any time.</p>
    </div>

    {{-- BAGIAN TANDA TANGAN & QR CODE --}}
    <div class="footer-section">
        <div class="qr-section">
            @if($protocol->certificate_uuid)
                <div style="display: inline-block; text-align: center;">
                    <div class="qr-code-wrapper">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate(route('certificates.verify', $protocol->certificate_uuid)) !!}
                    </div>
                    <div class="qr-label">Scan to Verify</div>
                </div>
            @endif
        </div>
        
        <div class="sign-section">
            <p>Surabaya, {{ $publishedDate->translatedFormat('d F Y') }}</p>
            <div class="sign-space"></div>
            <p class="sign-name">Caroline, S.Si., M.Si., Apt</p>
            <p class="sign-role">Chairperson</p>
        </div>
    </div>

</div>

{{-- Tombol Cetak (Hanya tampil di layar) --}}
<button class="print-btn no-print" onclick="window.print()">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M6 9V2h12v7H6zm-3 3h18v6H6v4H3v-4H0v-6h3zm15 0H6v4h12v-4z"/>
    </svg>
    Cetak Dokumen
</button>

</body>
</html>
