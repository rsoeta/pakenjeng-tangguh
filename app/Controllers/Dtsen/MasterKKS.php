<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class MasterKKS extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Master Data KKS',
            'list'  => $this->db->table('dtsen_master_kks')->get()->getResultArray()
        ];

        return view('dtsen/bansos_kks/v_master_kks', $data);
    }

    public function import_excel()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true); // Mematikan kalkulasi formula yang bikin error tadi

        $spreadsheet = $reader->load($file);
        // Parameter kedua false agar tidak mencoba menghitung formula (mengambil nilai mentah)
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, true, false);

        $count = 0;
        foreach ($sheetData as $key => $row) {
            // Lewati baris pertama (Header: Timestamp, Email, NIK, dll)
            if ($key == 0) continue;

            // Mapping berdasarkan struktur:
            // [0]Timestamp | [1]Email | [2]NIK | [3]Nama | [4]No.KKS | [5]WA | [6]Kepesertaan | [7]Status
            // [8]FotoKepemilikan | [9]Pernyataan | [10]KirimWA | [11]Alamat | [12]RT | [13]RW | [14]FotoKKS

            $nik = trim($row[2] ?? '');

            // Pastikan NIK tidak kosong sebelum diproses
            if (empty($nik)) continue;

            $data = [
                'nik'              => $nik,
                'nama_penerima'    => strtoupper(trim($row[3] ?? '')),
                'no_kks'           => trim($row[4] ?? ''),
                'no_wa'            => trim($row[5] ?? ''),
                'kepesertaan'      => trim($row[6] ?? ''),
                'status_kks'       => trim($row[7] ?? ''),
                'foto_kepemilikan' => trim($row[8] ?? ''), // Link/nama file foto orang pegang kartu
                'alamat'           => trim($row[11] ?? ''), // L-Alamat
                'rt'               => trim($row[12] ?? ''), // L-RT
                'rw'               => trim($row[13] ?? ''), // L-RW
                'foto_kks'         => trim($row[14] ?? ''), // Link/nama file foto fisik kartu
            ];

            // Replace akan mendeteksi NIK yang sama dan melakukan update, jika baru akan insert
            $this->db->table('dtsen_master_kks')->replace($data);
            $count++;
        }

        return redirect()->to('/master-kks')->with('success', "Sinkronisasi selesai. $count data KKS berhasil dipetakan ke database.");
    }
}
