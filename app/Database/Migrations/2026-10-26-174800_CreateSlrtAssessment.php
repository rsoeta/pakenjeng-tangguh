<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSlrtAssessment extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. TAMBAH KOLOM PEJABAT DI lembaga_profil (JIKA BELUM ADA)
        $fieldsLembaga = [];
        if (!$db->fieldExists('lp_puskesos_ketua', 'lembaga_profil')) {
            $fieldsLembaga['lp_puskesos_ketua'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'lp_nip',
            ];
        }
        if (!$db->fieldExists('lp_puskesos_hp', 'lembaga_profil')) {
            $fieldsLembaga['lp_puskesos_hp'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'lp_puskesos_ketua',
            ];
        }
        if (!$db->fieldExists('lp_supervisor_slrt', 'lembaga_profil')) {
            $fieldsLembaga['lp_supervisor_slrt'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'lp_puskesos_hp',
            ];
        }
        if (!$db->fieldExists('lp_supervisor_niat', 'lembaga_profil')) {
            $fieldsLembaga['lp_supervisor_niat'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'lp_supervisor_slrt',
            ];
        }
        if (!$db->fieldExists('lp_camat', 'lembaga_profil')) {
            $fieldsLembaga['lp_camat'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'lp_supervisor_niat',
            ];
        }
        if (!$db->fieldExists('lp_camat_nip', 'lembaga_profil')) {
            $fieldsLembaga['lp_camat_nip'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'lp_camat',
            ];
        }

        if (!empty($fieldsLembaga)) {
            $this->forge->addColumn('lembaga_profil', $fieldsLembaga);
        }

        // 2. BUAT TABEL slrt_assessment
        if (!$db->tableExists('slrt_assessment')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nomor_asesmen' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                ],
                'tanggal_asesmen' => [
                    'type' => 'DATE',
                ],
                'id_kk' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'no_kk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 25,
                ],
                'nik_sasaran' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                ],
                'nama_sasaran' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'nik_pembuat_pernyataan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                ],
                'nama_pembuat_pernyataan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'pembuat_pernyataan_hp' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'kategori_ppks_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'riwayat_penyakit' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'permasalahan_tambahan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'permohonan_bantuan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'foto_pasien' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'foto_depan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'foto_dalam' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'foto_kamar_mandi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'lampiran_siks' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'lampiran_faskes' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'fasilitator_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'fasilitator_nik' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                ],
                'ketua_puskesos_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'supervisor_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'supervisor_niat' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'kepala_desa_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'camat_nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'kode_desa' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'updated_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('nomor_asesmen');
            $this->forge->addKey('id_kk');
            $this->forge->addKey('nik_sasaran');
            $this->forge->addKey('kode_desa');
            $this->forge->createTable('slrt_assessment');
        }

        // 3. DAFTARKAN MENU UTAMA DI tb_menu
        $cekMenu = $db->table('tb_menu')->where('tm_url', 'assessment')->get()->getRowArray();
        if (!$cekMenu) {
            $db->table('tb_menu')->insert([
                'tm_nama'         => 'Assessment',
                'tm_class'        => 'assessment',
                'tm_url'          => 'assessment',
                'tm_icon'         => 'fas fa-file-medical-alt',
                'tm_parent_id'    => 0,
                'tm_status'       => 1,
                'tm_grup_akses'   => 4,
                'tm_urutan'       => 42,
                'tm_is_dashboard' => 0,
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('slrt_assessment', true);
        
        $db = \Config\Database::connect();
        $db->table('tb_menu')->where('tm_url', 'assessment')->delete();

        $dropCols = ['lp_puskesos_ketua', 'lp_puskesos_hp', 'lp_supervisor_slrt', 'lp_supervisor_niat', 'lp_camat', 'lp_camat_nip'];
        foreach ($dropCols as $col) {
            if ($db->fieldExists($col, 'lembaga_profil')) {
                $this->forge->dropColumn('lembaga_profil', $col);
            }
        }
    }
}
