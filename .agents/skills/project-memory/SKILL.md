---
name: project-memory
description: Log of project-specific prompts, architectural decisions, and custom workflow logic to maintain long-term context across sessions.
---

# Skill: Project Memory (SIKEPK Context)

Gunakan skill ini untuk mengingat kembali keputusan penting, riwayat prompt, dan aturan logika yang sudah disepakati di proyek ini.

---

## Aturan Penggunaan

1. **BACA SEBELUM BEKERJA**: Setiap kali memulai sesi baru atau mengerjakan fitur besar, baca file `LOG.md` di direktori skill ini.
2. **UPDATE SETELAH SELESAI**: Setelah menyelesaikan tugas besar (seperti sinkronisasi status atau perbaikan notifikasi), update `LOG.md` dengan ringkasan solusi yang diterapkan.
3. **Pahami "Pengecualian"**: Log di sini berisi alasan mengapa kode dibuat berbeda dari standar (misal: penggunaan `afterSave` vs `Observer`).

---

## Daftar Konten Memori

- **LOG.md**: Berisi catatan kronologis keputusan dan instruksi user.
- **CONVENTIONS.md**: (Optional) Berisi gaya pengkodean khusus untuk SIKEPK.

---

## Cara Update

Tambahkan entri baru di atas file `LOG.md` dengan format:

```markdown
### [DATE] - [Feature/Topic]
- **Prompt**: Ringkasan permintaan user.
- **Solution**: Bagaimana cara kita menyelesaikannya.
- **Critical Logic**: Hal-hal yang tidak boleh diubah tanpa diskusi.
```
