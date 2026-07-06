<x-mail::message>
# Revisi Diperlukan

Halo **{{ $protocol->User->name }}**,

Pengajuan protokol penelitian Anda dengan judul **"{{ $protocol->perihal_pengajuan }}"** membutuhkan revisi administratif/syarat sebelum dapat dilanjutkan ke proses telaah KEPK.

Berikut adalah catatan dari Sekretariat KEPK:
<x-mail::panel>
{{ $protocol->revision_notes }}
</x-mail::panel>

Silakan masuk ke dasbor SIM KEPK Anda untuk memperbaiki pengajuan tersebut dan mengirimkannya kembali.

<x-mail::button :url="config('app.url') . '/user'">
Masuk ke SIM KEPK
</x-mail::button>

Terima kasih,<br>
Sekretariat KEPK
</x-mail::message>
