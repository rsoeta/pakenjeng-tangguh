<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KemiskinanAlasanSeeder extends Seeder
{
    public function run()
    {
        $data = [

            // MISKIN
            ['kategori' => 'Aset', 'kode' => 'tidak_memiliki_rumah', 'label' => 'Tidak memiliki rumah', 'status_kemiskinan' => 'miskin', 'urutan' => 1],
            ['kategori' => 'Aset', 'kode' => 'tidak_memiliki_aset', 'label' => 'Tidak memiliki aset', 'status_kemiskinan' => 'miskin', 'urutan' => 2],
            ['kategori' => 'Aset', 'kode' => 'aset_sedikit', 'label' => 'Aset sedikit', 'status_kemiskinan' => 'miskin', 'urutan' => 3],

            ['kategori' => 'Rumah', 'kode' => 'rumah_tidak_layak', 'label' => 'Rumah tidak layak', 'status_kemiskinan' => 'miskin', 'urutan' => 4],
            ['kategori' => 'Rumah', 'kode' => 'rumah_kurang_layak', 'label' => 'Rumah kurang layak', 'status_kemiskinan' => 'miskin', 'urutan' => 5],

            ['kategori' => 'Keluarga', 'kode' => 'cerai_yatim', 'label' => 'Cerai hidup/mati / yatim', 'status_kemiskinan' => 'miskin', 'urutan' => 6],
            ['kategori' => 'Keluarga', 'kode' => 'anak_banyak', 'label' => 'Anak banyak / masih kecil', 'status_kemiskinan' => 'miskin', 'urutan' => 7],
            ['kategori' => 'Keluarga', 'kode' => 'sebatang_kara', 'label' => 'Hidup sebatang kara', 'status_kemiskinan' => 'miskin', 'urutan' => 8],

            ['kategori' => 'Pekerjaan', 'kode' => 'tidak_punya_pekerjaan', 'label' => 'Tidak punya pekerjaan', 'status_kemiskinan' => 'miskin', 'urutan' => 9],
            ['kategori' => 'Pekerjaan', 'kode' => 'serabutan', 'label' => 'Pekerjaan serabutan', 'status_kemiskinan' => 'miskin', 'urutan' => 10],

            ['kategori' => 'Ekonomi', 'kode' => 'hidup_dari_bantuan', 'label' => 'Hidup dari bantuan', 'status_kemiskinan' => 'miskin', 'urutan' => 11],
            ['kategori' => 'Ekonomi', 'kode' => 'hidup_tidak_layak', 'label' => 'Keseharian hidup tidak layak', 'status_kemiskinan' => 'miskin', 'urutan' => 12],

            ['kategori' => 'kesehatan', 'kode' => 'disabilitas', 'label' => 'Disabilitas', 'status_kemiskinan' => 'miskin', 'urutan' => 13],
            ['kategori' => 'kesehatan', 'kode' => 'penyakit_kronis', 'label' => 'Penyakit kronis', 'status_kemiskinan' => 'miskin', 'urutan' => 14],


            // TIDAK MISKIN
            ['kategori' => 'Aset', 'kode' => 'aset_cukup', 'label' => 'Memiliki aset cukup banyak', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 1],
            ['kategori' => 'Aset', 'kode' => 'aset_banyak', 'label' => 'Memiliki aset banyak', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 2],

            ['kategori' => 'Rumah', 'kode' => 'rumah_layak', 'label' => 'Rumah layak', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 3],
            ['kategori' => 'Rumah', 'kode' => 'rumah_mewah', 'label' => 'Rumah mewah', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 4],

            ['kategori' => 'Keluarga', 'kode' => 'anak_sedikit', 'label' => 'Anak sedikit', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 5],
            ['kategori' => 'Keluarga', 'kode' => 'tidak_memiliki_anak', 'label' => 'Tidak memiliki anak', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 6],

            ['kategori' => 'Pekerjaan', 'kode' => 'pekerja_tetap', 'label' => 'Memiliki pekerjaan tetap', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 7],
            ['kategori' => 'Pekerjaan', 'kode' => 'memiliki_usaha', 'label' => 'Memiliki usaha', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 8],
            ['kategori' => 'Pekerjaan', 'kode' => 'pns', 'label' => 'PNS/BUMN/BUMD', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 9],
            ['kategori' => 'Pekerjaan', 'kode' => 'pejabat', 'label' => 'Pejabat pemerintah', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 10],

            ['kategori' => 'Ekonomi', 'kode' => 'hidup_layak', 'label' => 'Keseharian hidup layak', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 11],
            ['kategori' => 'Ekonomi', 'kode' => 'hidup_mewah', 'label' => 'Keseharian hidup mewah', 'status_kemiskinan' => 'tidak_miskin', 'urutan' => 12],

        ];

        $this->db->table('dtsen_kemiskinan_alasan_master')->insertBatch($data);
    }
}
