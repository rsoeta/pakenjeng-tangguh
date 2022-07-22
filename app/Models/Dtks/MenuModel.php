<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;



class MenuModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'tb_menu';
    protected $primaryKey           = 'tm_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;

    protected $allowedFields        = [
        'tm_id',
        'tm_nama',
        'tm_class',
        'tm_url',
        'tm_icon',
        'tm_parent_id',
        'tm_status',
        'tm_grup_akses',
    ];

    // get uri
    function getUri()
    {
        $uri = $this->request->uri->getSegment(1);
        // dd($uri);
        return $uri;
    }

    public function getMenu($uri)
    {
        $uri = new \CodeIgniter\HTTP\URI(current_url());
        if ($uri->getSegment(1) == 'index.php') {
            $uri = $uri->getSegment(2);
        } else {
            $uri = $uri->getSegment(1);
        }
        // dd($uri);
        $menu = $this->db->table($this->table)
            ->where('tm_url', $uri)
            ->get()
            ->getResultArray();
        // dd($menu);
        return $menu;
    }

    public function load_data_menu()
    {
        $menu = $this->db->table($this->table)
            ->orderBy('tm_parent_id', 'asc')
            ->get()
            ->getResultArray();
        // dd($menu);
        return $menu;
    }

    public function insert_data_menu($data)
    {
        $this->db->table($this->table)->insert($data);
    }

    public function update_data_menu($id, $data)
    {
        $this->db->table($this->table)->update($data, ['tm_id' => $id]);
    }

    public function delete_data_menu($id)
    {
        // var_dump($id);
        $this->db->table($this->table)->delete(array('tm_id' => $id));
    }

    public function get_nama_menu($id)
    {
        $menu = $this->db->table($this->table)
            ->where('tm_id', $id)
            ->distinct()
            ->get()
            ->getResultArray();
        // dd($menu);
        foreach ($menu as $m) {
            $nama = $m['tm_nama'];
        }
        return $nama;
    }
}
