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
    // public $richFolder = '';

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

    // public function __construct()
    // {
    //     // Setup untuk RichRenderer (browser)
    //     RichRenderer::$folder = $this->richFolder;
    //     RichRenderer::$theme  = $this->richTheme;

    //     \Kint\Kint::$file_link_format = '%f:%l';

    //     // Setup untuk TextRenderer (CLI)
    //     if (property_exists(TextRenderer::class, 'decorations')) {
    //         TextRenderer::$decorations = false;
    //     }
    // }

    public function __construct()
    {
        RichRenderer::$folder = $this->richFolder;
        RichRenderer::$theme  = $this->richTheme;

        // FIX PHP 8.x strict typing
        \Kint\Kint::$file_link_format = '%f:%l';

        if (property_exists(TextRenderer::class, 'decorations')) {
            TextRenderer::$decorations = false;
        }
    }

    public array $plugins = [
        'Kint\\Parser\\FsPathPlugin',
        'Kint\\Parser\\JsonPlugin',
        'Kint\\Parser\\TracePlugin',
        'Kint\\Parser\\ThrowablePlugin',
        'Kint\\Parser\\DateTimePlugin',
    ];
}
