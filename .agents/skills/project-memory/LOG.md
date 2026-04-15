# SIKEPK Project Memory Log

File ini mencatat keputusan penting dan instruksi khusus agar asisten AI memiliki ingatan jangka panjang terhadap proyek ini.

---

### 2026-04-15 - Sinkronisasi Status & Notifikasi Cerdas

**Konteks Utama**: 
Menyempurnakan alur sinkronisasi antara **Status Protokol** dan **Decision (Keputusan)** serta membereskan notifikasi yang bentrok.

#### 1. Sinkronisasi Otomatis (Status -> Decision)
- **Prompt**: "Decision pada table protocol masih menunjukan pending" saat status diubah menjadi Exempted/Certificate.
- **Solution**: 
    - Implementasi di `ProtocolObserver::updating` untuk mensinkronisasi `status_id` ke `fast_review_decision`.
    - Mapping: Status 1 & 5 -> "Exempted", Status 2 -> "Full Board", Status 3 -> "Expedited".
- **Critical Logic**: Jangan gunakan `updateQuietly` saat sinkronisasi karena akan mematikan log riwayat (Activity Log).

#### 2. Pencegahan Reset "Pending" yang Tidak Sengaja
- **Prompt**: "Saat saya mengubah status protocol menjadi exempted tidak terjadi perubahan di decisionnya. masih pending yang ditampilkan" (Ternyata menimpa/overwrite saat save).
- **Solution**: 
    - Edit `EditProtocol.php` & `CreateProtocol.php`.
    - Tambahkan pengecekan: Jika status protokol **BUKAN** Fast Review (ID 6), jangan paksa decision menjadi "Pending".
    - Tambahkan pengecekan: Jika penilai (ketua/sekertaris) tidak berubah (sama dengan DB), jangan lakukan reset atau penugasan ulang.

#### 3. Notifikasi Anti-Bentrok
- **Prompt**: "Notifikasi saat dipilih assign reviewer untuk kelompok... tidak muncul" dan "pastikan tidak bentrok dengan fast review".
- **Solution**:
    - **Pemisahan Logika**: Di `ProtocolObserver.php`, pengiriman notifikasi ke **seluruh anggota kelompok** hanya dilakukan jika status **BUKAN** Fast Review (ID 6).
    - **Spesialisasi Fast Review**: Notifikasi ke penilai Fast Review (2 orang) dikirim langsung dari `EditProtocol`/`CreateProtocol` setelah proses `attach` penilai selesai.
- **Critical Logic**: Gunakan relasi `assignedReviewerKelompok` dan kolom `nama_kelompok` (bukan `name`).

#### 4. Riwayat (Activity Log) yang Ramah Peneliti
- **Prompt**: "Dalam history ingin menampilkan proses changes... buat jadi mudah dipahami".
- **Solution**:
    - Update `ProtocolInfolist.php` bagian Tab 'History'.
    - Gunakan `formatStateUsing` dengan `getChangesAttribute()` dari Spatie.
    - Mapping ID ke teks (misal: ID 1 -> "EXEMPTED").
    - Bahasa Indonesia yang manusiawi: "• **Status Protokol** berubah dari 'SUBMISSION' menjadi **EXEMPTED**".

---

### Aturan Arsitektur SIKEPK

1. **Relation Names**: Gunakan `assignedReviewerKelompok` untuk kelompok penilai di model `Protocol`.
2. **Status IDs**: 1:EXEMPTED, 2:FULL BOARD, 3:EXPEDITED, 4:ON REVIEW, 5:CERTIFICATE, 6:FAST REVIEW, 7:SUBMISSION.
3. **UI Components**: Gunakan `Filament\Schemas\Components\Tabs` (Filament v4) daripada `Infolists`.
4. **Activity Logs**: Aktifkan `logAll()` dan `logOnlyDirty()` di semua model inti.
