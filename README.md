# Aplikasi Pakenjeng-Tangguh

Aplikasi ini di dedikasikan bagi masyarakat miskin untuk menjaga keberlangsungan jaminan sosialnya.

- untuk saat ini aplikasi ini terfokus pada pengelolaan Data Terpadu Kesejahteraan Sosial
- dalam memaksimalkan aplikasi ini setiap saat dikembangkan oleh para developer/programmer baik dalam program, fitur dan sistem

## Hal-hal yang perlu diperhatikan ketika pengisian pengajuan DTKS menggunakan Template Excel V.4

- Pastikan menggunakan data kependudukan terbaru dan sudah memiliki E - KTP dan KIA yang VALID (untuk anak usia 0 sampai 17 tahun);

- Aturan Pengusulan:

  - Untuk pengusulan baru (seluruh anggota keluarga belum sama sekali masuk dalam DTKS), seluruh anggota keluarganya di entri dalam Menu Usulan, lalu di export dalam template Excel yang program bansosnya di sesuaikan, contoh: IBU dengan Program BANSOS BPNT > Dimasukan pada file pengajuan BPNT, Anak dengan Program Bansos PBI atau NONBANSOS > Dimasukan pada file PBI atau NON BANSOS;
  - Untuk penambahan anggota keluarga, yang diusulkan hanya anggota keluarga yang belum masuk DTKS saja;
  - Untuk pengusulan Bansos yang sudah terdaftar DTKS hanya pengurus rumah tangga saja yang didaftarkan (BPNT / PKH) Karena Program Bansos Tersebut 1 Kartu Keluarga 1 Program BANSOS;
  - Untuk Program BANSOS PBI dapat diusulkannya perorangan dalam satu Kartu Keluarga (Terhitung perjiwa dalam satu Kartu Keluarga);
  - Setiap pengusulan proragam BANSOS harap dipisahkan filenya dengan cara sebagai berikut:
    - Pastikan kolom desa terisi oleh desa Anda
    - Pilih Program yang ingin di export lalu export

- Untuk Kolom Disabilitas apabila Status Disabilitas “ YA ”, Maka Masukan Kode Jenis Disabilitas Sebagai Berikut:

  - Kode Disabilitas Keterangan
    - PDFSK = Penyandang Disabilitas Fisik
    - PDINT Penyandang Disabilitas Intelektual
    - PDMTL Penyandang Disabilitas Mental
    - PDSNS Penyandang Disabilitas Sensorik
  - Apabila Disabilitas “ TIDAK ”, Maka Kode Disabilitas Dikosongkan (Tidak Diisi);

- Untuk Kolom Ibu Hamil apabila status Ibu Hamil “ YA ”, Masukan tanggal awal kehamilannya, apabila status Ibu Hamil “ TIDAK ”, Silakan Tanggalnya Dikosongkan (Tidak Diisi);

- Penulisan pada kolom NIK & KK wajib 16 digit, tidak kurang dan tidak lebih dan pastikan sesuai dengan data kependudukan yang bersangkutan;

- Penulisan tanggal sesuaikan dengan format (hari / bulan / tahun) contoh (21 / 12 / 1997);

- Penulisan nama tidak diperkenankan menggunakan karakter sebagai berikut: Kutip (“ ‘) koma (,) Titik (.) Garis Miring (/) dan tidak diperkenankan menggunakan gelar di bagian depan, terkecuali dalam penulisan KTP dan KK menggunakan karakter demikian;

- Setiap daerah hanya dapat mengirimkan 1 file usulan program BANSOS dalam satu periode (Satu Bulan),
  apabila dalam satu periode mengusulkan lebih dari satu usulan program BANSOS yang sama,
  secata otomatis usulan sebelumnya akan tertimpa dan dinyatakan tidak valid (tidak sah oleh sistem);

- Satu orang atau satu KK hanya dapat mendaftarkan 1 program Bansos pada satu Periode pengusulan (Satu Bulan), apabila orang tersebut didaftarkan lebih dari satu bansos dalam satu periode (meskipun berbeda template), secara otomatis usulan bansos sebelumnya akan tertimpa dan tidak valid (tidak sah oleh sistem);

- Apabila Upload lebih dari satu Desa / Kelurahan, file tersebut dapat digabungkan menjadi satu file usulan kecamatan untuk mempercepat proses usulan ( Penamaan file disesuaikan seperti pada nomor 2 point 5);

- Saat Upload data, dilampirkan juga berita acara Pengusulan data;

- Apabila pengumpulan data dan input data telah selesai, berkas dikirimkan melalui aplikasi SIKS-NG Online dengan ketentuan sebagai berikut:
  - Setiap ajuan yang terkirim akan di proses oleh sistem, hasil ajuan akan ditampilkan pada aplikasi SIKS-NG Online “Harap cek secara berkala SIKS-NG Online untuk melihat hasil ajuan”.
  - Upload File paling lambat dikirimkan di minggu terakhir sebelum tanggal 15 setiap bulannya, apabila melebihi tanggal yang telah ditentukan, maka akan diproses pada periode / bulan selanjutnya.

* Semua data yang dimasukan dan semua Program Bansos yang dipilih berupa usulan, tidak langsung otomatis menjadi penerima Bansos.

---

# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](http://codeigniter.com).

This repository holds the distributable version of the framework,
including the user guide. It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [the announcement](http://forum.codeigniter.com/thread-62615.html) on the forums.

The user guide corresponding to this version of the framework can be found
[here](https://codeigniter4.github.io/userguide/).

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the _public_ folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's _public_ folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter _public/..._, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use Github issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [_Contributing to CodeIgniter_](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 7.3 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- xml (enabled by default - don't turn it off)

# pakenjeng-tangguh
