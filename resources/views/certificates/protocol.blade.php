<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Exempted – {{ $protocol->perihal_pengajuan }}</title>
    <style>
        /* ─── Reset & Base ─────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f5f5f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* ─── Certificate Card ────────────────────────────────────── */
        .certificate {
            background: #fff;
            width: 794px; /* A4 width at 96dpi */
            min-height: 1123px; /* A4 height */
            padding: 60px 70px;
            border: 2px solid #15803d;
            box-shadow: 0 8px 40px rgba(0,0,0,0.15);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* ─── Corner decorations ──────────────────────────────────── */
        .certificate::before,
        .certificate::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #15803d;
            border-style: solid;
        }
        .certificate::before { top: 12px; left: 12px; border-width: 3px 0 0 3px; }
        .certificate::after  { bottom: 12px; right: 12px; border-width: 0 3px 3px 0; }

        /* ─── Header / Kop ───────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 3px double #15803d;
            margin-bottom: 30px;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: 2px solid #15803d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #15803d;
            text-align: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .institution {
            flex: 1;
            text-align: center;
        }

        .institution h1 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111827;
        }

        .institution p {
            font-size: 11.5px;
            color: #374151;
            margin-top: 2px;
        }

        /* ─── Certificate Title ─────────────────────────────────── */
        .title-section {
            text-align: center;
            margin: 30px 0 28px;
        }

        .cert-label {
            font-size: 13px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .cert-title {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            color: #14532d;
            margin: 6px 0;
            letter-spacing: 2px;
        }

        .cert-subtitle {
            font-size: 14px;
            color: #374151;
        }

        /* ─── Divider ───────────────────────────────────────────── */
        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #15803d, transparent);
            margin: 10px auto 28px;
            width: 70%;
        }

        /* ─── Body Text ─────────────────────────────────────────── */
        .body-text {
            font-size: 14px;
            color: #374151;
            line-height: 1.8;
            text-align: center;
            margin-bottom: 24px;
        }

        /* ─── Recipient Name ────────────────────────────────────── */
        .recipient-name {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #14532d;
            font-family: 'Georgia', serif;
            border-bottom: 2px solid #15803d;
            display: inline-block;
            padding: 4px 40px 8px;
            margin: 8px auto 24px;
        }

        .recipient-wrap {
            text-align: center;
            margin-bottom: 24px;
        }

        /* ─── Protocol Details Table ────────────────────────────── */
        .detail-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px 28px;
            margin: 0 20px 32px;
        }

        .detail-box table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .detail-box td {
            padding: 5px 8px;
            vertical-align: top;
            color: #374151;
        }

        .detail-box td:first-child {
            font-weight: bold;
            color: #14532d;
            width: 180px;
            white-space: nowrap;
        }

        .detail-box td:nth-child(2) {
            width: 10px;
            font-weight: bold;
        }

        /* ─── Status Badge ──────────────────────────────────────── */
        .status-badge {
            display: inline-block;
            background: #15803d;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            padding: 4px 18px;
            border-radius: 999px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 32px;
        }

        .status-wrap { text-align: center; }

        /* ─── Signature Area ────────────────────────────────────── */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 20px;
        }

        .sig-block {
            text-align: center;
            width: 180px;
        }

        .sig-line {
            height: 1px;
            background: #374151;
            margin-bottom: 6px;
        }

        .sig-name {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
        }

        .sig-role {
            font-size: 11px;
            color: #6b7280;
        }

        /* ─── Footer ────────────────────────────────────────────── */
        .cert-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10.5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }

        /* ─── Print Styles ────────────────────────────────────── */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .certificate {
                width: 100%;
                min-height: 100vh;
                box-shadow: none;
                padding: 40px 50px;
                border-width: 1px;
                page-break-inside: avoid;
            }

            .print-btn { display: none !important; }
        }

        /* ─── Print Button (screen only) ──────────────────────── */
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
    </style>
</head>
<body>

<div class="certificate">

    {{-- ── Header / Kop ──────────────────────────────────────────── --}}
    <div class="header">
        <div class="logo-placeholder">LOGO<br>INSTITUSI</div>
        <div class="institution">
            <h1>Komite Etik Penelitian Kesehatan</h1>
            <p>Sistem Informasi Manajemen Kode Etik Penelitian Kesehatan (SIMKEPK)</p>
            <p>Jl. Contoh Alamat No. 1, Kota, Provinsi – 12345 | Telp. (021) 000-0000</p>
        </div>
    </div>

    {{-- ── Certificate Title ──────────────────────────────────────── --}}
    <div class="title-section">
        <div class="cert-label">Menerangkan bahwa</div>
        <div class="cert-title">Sertifikat Exempted</div>
        <div class="cert-subtitle">Protokol Penelitian Telah Dinyatakan Lolos Kaji Etik (Exempted)</div>
    </div>

    <div class="divider"></div>

    {{-- ── Recipient ─────────────────────────────────────────────── --}}
    <p class="body-text">Diberikan kepada peneliti:</p>

    <div class="recipient-wrap">
        <div class="recipient-name">{{ $nama_lengkap }}</div>
    </div>

    <p class="body-text" style="margin-bottom: 20px;">
        Telah mengajukan protokol penelitian yang tercatat dalam sistem dan telah
        memenuhi seluruh persyaratan kaji etik, sehingga dinyatakan:
    </p>

    <div class="status-wrap">
        <span class="status-badge">✓ Exempted</span>
    </div>

    {{-- ── Detail Protokol ─────────────────────────────────────── --}}
    <div class="detail-box">
        <table>
            <tr>
                <td>Judul / Perihal</td>
                <td>:</td>
                <td>{{ $protocol->perihal_pengajuan }}</td>
            </tr>
            <tr>
                <td>Jenis Protokol</td>
                <td>:</td>
                <td>{{ $protocol->jenis_protocol ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($protocol->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>{{ $protocol->statusReview?->status_name ?? 'Exempted' }}</td>
            </tr>
            <tr>
                <td>Tanggal Terbit</td>
                <td>:</td>
                <td>{{ now()->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- ── Signature ─────────────────────────────────────────────── --}}
    <div class="signatures">
        <div class="sig-block">
            <div style="height: 60px;"></div>
            <div class="sig-line"></div>
            <div class="sig-name">Ketua Komite Etik</div>
            <div class="sig-role">SIMKEPK</div>
        </div>
        <div class="sig-block" style="text-align:center;">
            <p style="font-size:12px; color:#6b7280;">Diterbitkan pada:</p>
            <p style="font-size:13px; font-weight:bold; color:#111827; margin-top:4px;">
                {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
        <div class="sig-block">
            <div style="height: 60px;"></div>
            <div class="sig-line"></div>
            <div class="sig-name">Sekretaris</div>
            <div class="sig-role">SIMKEPK</div>
        </div>
    </div>

    {{-- ── Footer ────────────────────────────────────────────────── --}}
    <div class="cert-footer">
        Dokumen ini diterbitkan secara resmi oleh SIMKEPK · Sertifikat No. CERT-{{ str_pad($protocol->id, 5, '0', STR_PAD_LEFT) }}/{{ now()->year }}
    </div>

</div>

{{-- ── Print Button (screen only) ─────────────────────────────── --}}
<button class="print-btn" onclick="window.print()">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M6 9V2h12v7H6zm-3 3h18v6H6v4H3v-4H0v-6h3zm15 0H6v4h12v-4z"/>
    </svg>
    Cetak Sertifikat
</button>

</body>
</html>
