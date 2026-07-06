<x-mail::message>
# Status Protokol Diperbarui

Halo **{{ $protocol->User->name }}**,

Status pengajuan protokol penelitian Anda dengan judul **"{{ $protocol->perihal_pengajuan }}"** telah diperbarui.

**Status Saat Ini:** {{ $protocol->statusReview->status_name ?? 'Diproses' }}

Silakan masuk ke dasbor SIM KEPK Anda untuk melihat detail perkembangan protokol Anda.

<x-mail::button :url="config('app.url') . '/user'">
Masuk ke SIM KEPK
</x-mail::button>

Terima kasih,<br>
Sekretariat KEPK
</x-mail::message>
