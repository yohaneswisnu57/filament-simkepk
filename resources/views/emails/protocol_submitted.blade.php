<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Protokol SIM-KEPK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:32px 0;">
    <tr>
        <td align="center">

            <!-- Container -->
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:600px;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#0f172a;padding:20px;text-align:center;">
                        <h1 style="margin:0;font-size:20px;color:#ffffff;font-weight:600;">
                            SIM-KEPK
                        </h1>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:24px;color:#374151;font-size:14px;line-height:1.6;">

                        <p style="margin:0 0 16px 0;">
                            Yth. <strong>Peneliti / Admin KEPK</strong>,
                        </p>

                        <p style="margin:0 0 16px 0;">
                            Berikut adalah pemberitahuan bahwa <strong>Protokol Etik Penelitian Kesehatan</strong>
                            baru telah berhasil diajukan melalui Sistem Informasi Manajemen KEPK (SIM-KEPK).
                        </p>

                        <!-- Info Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                            <tr>
                                <td style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:16px;">
                                    <p style="margin:0;font-size:13px;color:#374151;">
                                        <strong>Detail Pengajuan:</strong><br><br>
                                        <strong>Nama Peneliti:</strong> {{ $protocol->user->name ?? 'Unknown' }}<br>
                                        <strong>Judul Protokol:</strong> {{ $protocol->perihal_pengajuan }}<br>
                                        <strong>Tanggal Pengajuan:</strong> {{ $protocol->tanggal_pengajuan }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 20px 0;">
                            Bagi Admin KEPK, mohon untuk segera melakukan <strong>verifikasi dan penugasan penelaah</strong>. Bagi Peneliti, mohon pantau status pengajuan Anda melalui dasbor SIM KEPK.

                            <p>Silakan klik tombol di bawah ini untuk melihat detail:</p>

                            <a href="{{ \App\Filament\Resources\Protocols\ProtocolResource::getUrl('edit', ['record' => $protocol]) }}"
                            style="background-color: #d97706; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                            Cek Protokol
                            </a>
                        </p>

                        <!-- Button -->
                        <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background-color:#0f172a;border-radius:6px;">
                                    <a href="#"
                                       style="display:inline-block;padding:12px 24px;
                                              color:#ffffff;text-decoration:none;
                                              font-size:14px;font-weight:600;">
                                        Buka SIM-KEPK
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0;">
                            Demikian informasi ini kami sampaikan.<br>
                            Atas perhatian Bapak/Ibu, kami ucapkan terima kasih.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 32px;">
                            <tr>
                                <td style="width: 50%;"></td>
                                <td style="width: 50%; text-align: left; color:#374151; font-size:14px; line-height:1.6;">
                                    <p style="margin: 0;">Surabaya, {{ now()->translatedFormat('d F Y') }}</p>
                                    <p style="margin: 0; height: 70px;"></p>
                                    <p style="margin: 0; font-weight: normal;">Caroline, S.Si., M.Si., Apt</p>
                                    <p style="margin: 0; font-weight: bold;">Chairperson</p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f9fafb;padding:16px;text-align:center;
                               font-size:12px;color:#6b7280;">
                        © 2026 SIM-KEPK. Seluruh hak cipta dilindungi.
                    </td>
                </tr>

            </table>
            <!-- End Container -->

        </td>
    </tr>
</table>

</body>
</html>
