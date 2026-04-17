---
name: ui-ux-promax
description: Panduan UI/UX tingkat lanjut (ProMax) untuk FilamentPHP. Fokus pada pembuatan antarmuka yang premium, responsif, dan sangat ramah pengguna (user-friendly) untuk peneliti dan admin SIKEPK.
---

# Skill: UI/UX ProMax (Premium Filament Design)

Gunakan skill ini setiap kali diminta untuk merancang, memperbaiki, atau meningkatkan antarmuka pengguna (UI) dan pengalaman pengguna (UX) di proyek SIKEPK. Tujuannya adalah menciptakan pengalaman "WOW" dan mempermudah alur kerja pengguna (terutama peneliti yang mungkin tidak terbiasa dengan sistem rumit).

---

## Prinsip Dasar UI/UX ProMax

1. **Kejelasan di Atas Segalanya (Clarity First)**
2. **Visual Hierarchy (Hierarki Visual yang Jelas)**
3. **Pencegahan Kesalahan (Error Prevention > Error Handling)**
4. **Umpan Balik Instan & Jelas (Clear Feedback)**

---

## Panduan Penerapan di FilamentPHP

### 1. Form Design (Pengalaman Mengisi Data)

- **Gunakan `Section` dan `Grid`**: Jangan biarkan form terlalu panjang ke bawah tanpa pemisah. Kelompokkan input yang logis ke dalam `Section::make('Judul')` dengan deskripsi singkat `->description('Bantu user memahami bagian ini')`.
- **Kolom (Columns)**: Gunakan `->columns(2)` atau `->columns(3)` secara responsif. Jangan meletakkan input pendek (seperti NIK) dalam 1 kolom penuh (full width).
- **Placeholder & Hint**: Gunakan `->placeholder('Contoh: 123456789')` dan `->hint('Instruksi ringkas tambahan')` untuk mengurangi kebingungan.
- **Validasi Intuitif**: Berikan pesan error yang manusiawi. Jangan gunakan pesan teknis.

### 2. Table & List Design (Menyajikan Data)

- **Gunakan Badge untuk Status**: Status (seperti Exempted, Full Board, Pending) HARUS menggunakan `BadgeColumn` atau `TextColumn::make()->badge()` dengan warna yang sesuai (Hijau untuk sukses, Kuning/Orange untuk proses, Merah untuk ditolak/bahaya).
- **Prioritaskan Kolom Penting**: Tampilkan maksimal 5-6 kolom utama di tabel. Sembunyikan informasi detail di balik fungsi Toggle: `->toggleable(isToggledHiddenByDefault: true)`.
- **Format Data yang Nyaman Dibaca**:
  - Tanggal: Tampilkan dengan format yang manusiawi (misal: `d M Y` -> "15 Apr 2026").
  - Angka/Uang: Format sebagai nominal yang mudah dibaca.
- **Empty State**: Jika tabel kosong, pastikan ada ikon dan pesan yang ramah, contoh: "Belum ada protokol yang didaftarkan."

### 3. Infolist (Detail View)

- **Gunakan Layout Lanjutan**: Hindari daftar panjang teks rata kiri. Gunakan `Grid`, `Section`, dan `Tabs` (seperti yang dilakukan pada History Protokol).
- **Ikon**: Tambahkan ikon yang relevan pada `TextEntry` (misal: ikon kalender untuk tanggal, ikon dokumen untuk file upload).
- **Tombol Aksi (Action Buttons) yang Kontekstual**: Letakkan tombol aksi primer (seperti "Cetak Sertifikat") di tempat yang sangat menonjol. Beri label aksi sekunder dengan warna abu-abu atau *outline*. Bedakan aksi *destructive* (Hapus, Tolak) dengan warna merah dan selalu sertakan konfirmasi (`->requiresConfirmation()`).

### 4. Notifikasi & Feedback

- **Spesifik dan Dapat Ditindaklanjuti (Actionable)**: Daripada "Protokol berhasil disimpan", gunakan "Protokol 'Uji Malaria' berhasil diajukan untuk Fast Review."
- **Sertakan Tombol di Notifikasi**: Jika notifikasi muncul di *database notification*, pastikan ada tombol (Action) yang mengarahkan mereka langsung ke halaman terkait.

### 5. Komunikasi Data (Human-Readable)

- **Hindari ID/Kode Teknis di UI**: Sembunyikan Foreign Keys (`user_id`, `status_id`). Selalu tampilkan labelnya (`Peneliti: John Doe`, `Status: FULL BOARD`).
- **History/Log yang Bercerita**: Jika menampilkan jejak audit (Activity Log), terjemahkan perubahan ke dalam kalimat bahasa manusia (seperti yang diimplementasikan pada `ProtocolInfolist`).

---

## Aturan Emas Saat Memperbaiki UI di SIKEPK

Setiap kali Anda (AI) mengusulkan perubahan kode tampilan, tanyakan pada diri sendiri:
> **"Apakah peneliti awam akan langsung paham tombol mana yang harus ditekan dan apa status data mereka dari lirikan mata pertama?"**
Jika jawabannya ragu, perbaiki desain Anda untuk lebih *ProMax*.
