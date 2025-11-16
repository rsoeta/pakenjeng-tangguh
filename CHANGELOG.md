# Changelog – SINDEN DTSEN

## v3_submittedData_fix_routing_payloadStrict — 2025-11-16

### 🚀 Highlights

Perbaikan besar pada mekanisme pemisahan **Draft Pembaruan** dan **Submitted Pembaruan** agar data tidak salah masuk ke tab submitted meskipun payload belum lengkap.

### 🔧 Perubahan Utama

- Memperbaiki routing:
  - Route `/pembaruan-keluarga/data` kini diarahkan ke `PembaruanKeluarga::data()` agar dapat memproses query string `?submitted=1` dan `?status=draft`.
- Menambahkan `ResponseTrait` pada controller `PembaruanKeluarga` untuk mengatasi error `undefined method respond()`.
- Memperbaiki method `getSubmittedData()`:
  - Menambahkan validasi payload ketat untuk memastikan:
    - Field JSON tidak kosong (`NULL`, string kosong, array kosong, atau object kosong).
    - `kondisi`, `wilayah`, `sanitasi`, dan field wajib lain terisi.
    - Payload ART lengkap (identitas, pendidikan, kesehatan, tenaga kerja).
- Memperbaiki DataTables untuk tab Submitted agar memanggil endpoint yang tepat.
- Menambah pengecekan logika kelengkapan sebelum data dianggap "Submitted".

### 🖼️ Frontend & View

- Update tab baru **Submitted Pembaruan** pada `dtsen/se/index.php`.
- Update tampilan & script DataTables untuk:
  - `tableDraftKeluarga`
  - `tableSubmitted`
- Menyesuaikan beberapa komponen form:
  - `modal_anggota.php`
  - `tab_keluarga.php`
  - `tab_rumah.php`
  - `detail.php`

### 🗂️ File & Struktur

- Menghapus file lama `app/Views/dtsen/se/index_old.php`.
- Update `.gitignore`.
- Update `public/assets/dist/css/style.css`.

---

## v2_tabSubmitted_initial — 2025-11-15

> (catatan opsional, isi jika kamu ingin mendokumentasikan versi sebelumnya)

- Penambahan tab Submitted Pembaruan.
- Implementasi awal DataTables Submitted.
- Implementasi routing sementara.

---

## v1_initialMigration_dtsenRefactor — 2025-11-10

> (catatan opsional...)

- Migrasi awal pembaruan DTSEN.
- Penyesuaian struktur controller & view.

---

# Format

Changelog ini mengikuti pola:

- **Header versi** → menggunakan style versi internal proyek (v3_xxx)
- **Tanggal rilis**
- **Highlights** → ringkasan
- **Perubahan Utama** → technical changes
- **Frontend / Backend** → detail perubahan
