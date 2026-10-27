<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class LansiaModel extends Model
{
	use WilayahFilterTrait;

	protected $table = 'dtsen_art';
	protected $primaryKey = 'id_art';
	protected $allowedFields = [
		'id_kk', 'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
		'status_kawin', 'pendidikan_terakhir', 'pekerjaan', 'disabilitas'
	];

	var $column_order = [
		null,
		'art.nik',
		'art.nama',
		'alamat',
		'rt.rw',
		'usia',
		'art.jenis_kelamin',
		'kr.kategori_rumah',
		'pk.pk_nama',
		'kategori_lansia',
		null
	];

	var $order = ['art.nama' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->ensureLansiaTable();
	}

	/**
	 * Pastikan tabel dtsen_lansia otomatis dibuat jika belum ada di database hosting
	 */
	public function ensureLansiaTable(): void
	{
		try {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `dtsen_lansia` (
					`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					`id_art` INT UNSIGNED NOT NULL,
					`kategori` ENUM('Lansia', 'Jompo') NOT NULL DEFAULT 'Lansia',
					`keterangan` VARCHAR(255) NULL,
					`created_by` VARCHAR(100) NULL,
					`updated_by` VARCHAR(100) NULL,
					`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
					`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					UNIQUE KEY `uk_id_art` (`id_art`),
					INDEX `idx_kategori` (`kategori`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
			");
		} catch (\Throwable $e) {
			log_message('warning', 'ensureLansiaTable warning: ' . $e->getMessage());
		}
	}

	private function _get_datatables_query(array $filter, int $roleId = 1)
	{
		$hasLansiaTable       = $this->db->tableExists('dtsen_lansia');
		$hasKeadaanRumahTable = $this->db->tableExists('dtsen_keadaan_rumah');
		$hasPendidikanTable   = $this->db->tableExists('pendidikan_kk');
		$hasUsulanArtTable    = $this->db->tableExists('dtsen_usulan_art');

		$builder = $this->db->table('dtsen_art art');

		$selectFields = [
			'art.id_art',
			'art.id_kk',
			'art.nama',
			'art.nik',
			'art.jenis_kelamin',
			'art.tanggal_lahir',
			'TIMESTAMPDIFF(YEAR, art.tanggal_lahir, CURDATE()) AS usia',
			'COALESCE(NULLIF(rt.alamat, \'\'), NULLIF(kk.alamat, \'\'), \'-\') AS alamat',
			'COALESCE(NULLIF(rt.rt, \'\'), \'000\') AS rt',
			'COALESCE(NULLIF(rt.rw, \'\'), \'000\') AS rw',
			'rt.kode_desa',
		];

		if ($hasPendidikanTable && $hasUsulanArtTable) {
			$selectFields[] = "COALESCE(pk.pk_nama, pk_u.pk_nama, NULLIF(art.pendidikan_terakhir, ''), '-') AS pendidikan";
		} elseif ($hasPendidikanTable) {
			$selectFields[] = "COALESCE(pk.pk_nama, NULLIF(art.pendidikan_terakhir, ''), '-') AS pendidikan";
		} else {
			$selectFields[] = "COALESCE(NULLIF(art.pendidikan_terakhir, ''), '-') AS pendidikan";
		}

		if ($hasKeadaanRumahTable) {
			$selectFields[] = "COALESCE(NULLIF(kr.kategori_rumah, ''), '-') AS kategori_rumah";
			$selectFields[] = "COALESCE(NULLIF(kr.status_kepemilikan, ''), NULLIF(rt.kepemilikan_rumah, ''), '-') AS status_kepemilikan";
		} else {
			$selectFields[] = "'-' AS kategori_rumah";
			$selectFields[] = "COALESCE(NULLIF(rt.kepemilikan_rumah, ''), '-') AS status_kepemilikan";
		}

		if ($hasLansiaTable) {
			$selectFields[] = "COALESCE(ls.kategori, 'Lansia') AS kategori_lansia";
			$selectFields[] = "ls.keterangan AS keterangan_lansia";
		} else {
			$selectFields[] = "'Lansia' AS kategori_lansia";
			$selectFields[] = "NULL AS keterangan_lansia";
		}

		$builder->select(implode(",\n\t\t\t", $selectFields));

		$builder->join('dtsen_kk kk', 'kk.id_kk = art.id_kk');
		$builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

		if ($hasKeadaanRumahTable) {
			$builder->join('dtsen_keadaan_rumah kr', 'kr.id_kk = kk.id_kk', 'left');
		}

		if ($hasPendidikanTable) {
			$builder->join('pendidikan_kk pk', 'pk.pk_id = art.pendidikan_terakhir', 'left');
		}

		if ($hasPendidikanTable && $hasUsulanArtTable) {
			$subqueryUsulan = '(
				SELECT ua.nik, JSON_UNQUOTE(JSON_EXTRACT(ua.payload_member, \'$.identitas.pendidikan_terakhir\')) AS usulan_pend_id
				FROM dtsen_usulan_art ua
				INNER JOIN (
					SELECT nik, MAX(id) AS max_id 
					FROM dtsen_usulan_art 
					WHERE payload_member IS NOT NULL 
					GROUP BY nik
				) latest ON latest.max_id = ua.id
			) u_pend';
			$builder->join($subqueryUsulan, 'u_pend.nik = art.nik', 'left', false);
			$builder->join('pendidikan_kk pk_u', 'pk_u.pk_id = u_pend.usulan_pend_id', 'left');
		}

		if ($hasLansiaTable) {
			$builder->join('dtsen_lansia ls', 'ls.id_art = art.id_art', 'left');
		}

		// Hanya lansia (usia > 60 tahun) dan tidak terhapus
		$builder->where('art.deleted_at IS NULL');
		$builder->where('kk.deleted_at IS NULL');
		$builder->where('TIMESTAMPDIFF(YEAR, art.tanggal_lahir, CURDATE()) >', 60);

		// 🔐 Terapkan WilayahFilterTrait (Karantina Kode Desa, RW, RT, dan Wilayah Tugas Petugas)
		$this->applyWilayahFilter($builder, $filter, $roleId);

		// Filter Kategori (Lansia / Jompo)
		$kategori = $filter['kategori'] ?? '';
		if (!empty($kategori)) {
			if ($hasLansiaTable) {
				if ($kategori === 'Lansia') {
					$builder->groupStart()
						->where('ls.kategori', 'Lansia')
						->orWhere('ls.kategori IS NULL')
						->groupEnd();
				} elseif ($kategori === 'Jompo') {
					$builder->where('ls.kategori', 'Jompo');
				}
			} elseif ($kategori === 'Jompo') {
				$builder->where('1 = 0', null, false);
			}
		}

		// Pencarian
		$search = $filter['search'] ?? ($_POST['search']['value'] ?? '');
		if ($search !== '') {
			$builder->groupStart()
				->like('art.nama', $search)
				->orLike('art.nik', $search)
				->orLike('kk.no_kk', $search)
				->orLike('rt.alamat', $search)
				->orLike('kk.alamat', $search);
			if ($hasLansiaTable) {
				$builder->orLike('ls.keterangan', $search);
			}
			$builder->groupEnd();
		}

		return $builder;
	}

	public function get_datatables(array $filter, int $roleId = 1)
	{
		$builder = $this->_get_datatables_query($filter, $roleId);

		if (isset($_POST['order']) && isset($_POST['order']['0']['column'])) {
			$colIdx = (int)$_POST['order']['0']['column'];
			$colName = $this->column_order[$colIdx] ?? null;
			if ($colName) {
				$dir = $_POST['order']['0']['dir'] ?? 'asc';
				$builder->orderBy($colName, $dir);
			} else {
				$builder->orderBy('art.nama', 'asc');
			}
		} else if ($this->order) {
			$order = $this->order;
			$builder->orderBy(key($order), $order[key($order)]);
		}

		$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
		$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
		if ($length !== -1) {
			$builder->limit($length, $start);
		}

		return $builder->get()->getResult();
	}

	public function jumlah_semua(array $filter, int $roleId = 1)
	{
		$builder = $this->db->table('dtsen_art art');
		$builder->join('dtsen_kk kk', 'kk.id_kk = art.id_kk');
		$builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');

		$builder->where('art.deleted_at IS NULL');
		$builder->where('kk.deleted_at IS NULL');
		$builder->where('TIMESTAMPDIFF(YEAR, art.tanggal_lahir, CURDATE()) >', 60);

		// Karantina wilayah dasar (kode_desa, wilayah_tugas, level) sesuai hak akses user
		$filterWilayah = [
			'kode_desa'     => $filter['kode_desa'] ?? '',
			'wilayah_tugas' => $filter['wilayah_tugas'] ?? '',
			'level'         => $filter['level'] ?? ''
		];
		$this->applyWilayahFilter($builder, $filterWilayah, $roleId);

		return (object)['jml' => $builder->countAllResults()];
	}

	public function jumlah_filter(array $filter, int $roleId = 1)
	{
		$builder = $this->_get_datatables_query($filter, $roleId);
		return (object)['jml' => $builder->countAllResults()];
	}

	public function get_export_data(array $filter, int $roleId = 1)
	{
		$builder = $this->_get_datatables_query($filter, $roleId);
		$builder->orderBy('art.nama', 'asc');
		return $builder->get()->getResultArray();
	}

	public function save_status_lansia($id_art, $kategori, $keterangan, $username = '')
	{
		$this->ensureLansiaTable();
		$db = \Config\Database::connect();
		$sql = "
			INSERT INTO dtsen_lansia (id_art, kategori, keterangan, updated_by)
			VALUES (?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE 
				kategori = VALUES(kategori), 
				keterangan = VALUES(keterangan), 
				updated_by = VALUES(updated_by), 
				updated_at = NOW()
		";
		return $db->query($sql, [$id_art, $kategori, $keterangan, $username]);
	}
}
