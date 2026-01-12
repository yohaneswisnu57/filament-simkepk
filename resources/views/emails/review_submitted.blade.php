<!DOCTYPE html>
<html>
<head>
    <title>Update Progress Review</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h3>Yth. {{ $protocol->user->name ?? 'Peneliti' }},</h3>

    <p>Kami menginformasikan bahwa proses review untuk protokol Anda telah mengalami kemajuan.</p>

    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
        <strong>Detail Protokol:</strong>
        <ul style="list-style: none; padding-left: 0;">
            <li><strong>Judul:</strong> {{ $protocol->perihal_pengajuan }}</li>
            <li><strong>Status Update:</strong> Salah satu penelaah ({{ $reviewerName }}) telah mengirimkan hasil review.</li>
        </ul>
    </div>

    <p>
        Saat ini protokol Anda sedang dalam proses rekapitulasi keputusan.
        Anda akan menerima notifikasi lebih lanjut setelah keputusan final (Full Board / Expedited) diterbitkan oleh Ketua KEPK.
    </p>

    <p>Silakan login ke aplikasi SIMKEPK untuk memantau status terkini.</p>

    <br>

    <a href="{{ url('/admin/login') }}"
       style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
       Login ke SIMKEPK
    </a>

    <p style="margin-top: 30px; font-size: 12px; color: #777;">
        Email ini dikirim secara otomatis oleh Sistem SIMKEPK. Mohon tidak membalas email ini.
    </p>

</body>
</html>
