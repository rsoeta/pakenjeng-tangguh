<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $DBGroup      = 'default';
    protected $table        = 'tb_menu';
    protected $primaryKey   = 'tm_id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'tm_nama',
        'tm_class',
        'tm_url',
        'tm_icon',
        'tm_parent_id',
        'tm_status',
        'tm_grup_akses',
    ];

    /**
     * ============================================================
     * FINAL: getMenu()
     * ============================================================
     * - TIDAK lagi membaca URI dari request (itu tugas Filter)
     * - Menerima parameter $uri langsung dari MenuFilterDtks
     * - Mencari data berdasarkan tm_url (1 segmen, misal: "pemeriksaan")
     */
    public function getMenu($uri)
    {
        return $this->where('tm_url', $uri)->findAll();
    }


    /**
     * ============================================================
     * Load semua menu (dipakai di panel admin)
     * ============================================================
     */
    public function load_data_menu()
    {
        return $this->orderBy('tm_parent_id', 'asc')
            ->orderBy('tm_id', 'asc')
            ->findAll();
    }


    /**
     * ============================================================
     * Insert menu
     * ============================================================
     */
    public function insert_data_menu($data)
    {
        return $this->insert($data);
    }


    /**
     * ============================================================
     * Update menu
     * ============================================================
     */
    public function update_data_menu($id, $data)
    {
        return $this->update($id, $data);
    }


    /**
     * ============================================================
     * Delete menu
     * ============================================================
     */
    public function delete_data_menu($id)
    {
        return $this->delete($id);
    }


    /**
     * ============================================================
     * Ambil nama menu berdasarkan tm_id
     * ============================================================
     */
    public function get_nama_menu($id)
    {
        $row = $this->select('tm_nama')
            ->where('tm_id', $id)
            ->first();

        return $row ? $row['tm_nama'] : null;
    }
}
