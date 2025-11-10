<?php

namespace Config;

use Kint\Renderer\RichRenderer;
use Kint\Renderer\TextRenderer;

class Kint extends \CodeIgniter\Config\BaseConfig
{
    /** Maksimum kedalaman dump */
    public $maxDepth = 6;

    /** Tampilkan file pemanggil */
    public $displayCalledFrom = true;

    /** Perluas struktur dump secara default */
    public $expanded = false;

    /** Tema untuk RichRenderer */
    public $richTheme = 'aante-light.css';

    /** Folder output untuk RichRenderer */
    public $richFolder = false;

    /** Urutkan hasil RichRenderer */
    public $richSort = true;

    /** Aktifkan warna pada output CLI */
    public $cliColors = true;

    /** Paksa output CLI menggunakan UTF-8 */
    public $cliForceUTF8 = true;

    /** Deteksi lebar terminal CLI */
    public $cliDetectWidth = true;

    /** Lebar minimum kolom CLI */
    public $cliMinWidth = 40;

    public function __construct()
    {
        // Setup untuk RichRenderer (browser)
        RichRenderer::$folder = $this->richFolder;
        RichRenderer::$theme  = $this->richTheme;

        // Setup untuk TextRenderer (CLI)
        if (property_exists(TextRenderer::class, 'decorations')) {
            TextRenderer::$decorations = false;
        }
    }
}
