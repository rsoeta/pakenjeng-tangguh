<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */


if (!function_exists('toTitleCase')) {
    /**
     * 🔠 Format string ke 'Capitalize Each Word' (Title Case) dengan penanganan akronim & tanda baca Indonesia
     */
    function toTitleCase(?string $string): string
    {
        if ($string === null || trim($string) === '' || trim($string) === '-') {
            return '-';
        }

        $string = trim($string);

        // Delimiter untuk ucwords: spasi, tab, newline, slash, strip, kurung, kurung siku, koma, titik
        $delimiters = " \t\r\n\f\v/-()[].,";

        // Step 1: Lowercase lalu ucwords dengan delimiter lengkap
        $words = ucwords(strtolower($string), $delimiters);

        // Step 2: Daftar singkatan/akronim umum yang harus tetap UPPERCASE
        $acronyms = [
            'RT', 'RW', 'NIK', 'KK', 'KTP', 'BPJS', 'KIS', 'PBI', 'PKH', 'KKS', 'KIP',
            'APBN', 'APBD', 'DTSEN', 'DTKS', 'SLRT', 'PMKS', 'PPKS', 'IRT', 'PNS',
            'TNI', 'POLRI', 'S.Sos', 'M.Si', 'S.Pd', 'S.IP', 'S.E', 'S.Kom', 'S.Ag', 'S.H',
            'S.Ked', 'dr.', 'drg.', 'Drs.', 'Dra.', 'H.', 'Hj.'
        ];

        foreach ($acronyms as $ac) {
            $pattern = '/\b' . preg_quote($ac, '/') . '\b/i';
            $words = preg_replace($pattern, $ac, $words);
        }

        // Step 3: Perbaiki singkatan jalan / kampung seperti Kp. dan Jl.
        $words = preg_replace('/\bKp\b/i', 'Kp.', $words);
        $words = preg_replace('/Kp\.\./i', 'Kp.', $words);
        $words = preg_replace('/\bJl\b/i', 'Jl.', $words);
        $words = preg_replace('/Jl\.\./i', 'Jl.', $words);

        return $words;
    }
}

/**
 * 🛡️ POLYFILL XMLWriter UNTUK SERVER HOSTING TANPA EKSTENSI ext-xmlwriter
 * Memastikan PhpSpreadsheet (Export Excel .xlsx) tetap berjalan normal meskipun
 * ekstensi PHP xmlwriter belum diaktifkan di cPanel / server produksi.
 */
if (!class_exists('XMLWriter', false)) {
    class XMLWriter
    {
        private string $buffer = '';
        private ?string $uri = null;
        private array $stack = [];
        private bool $inOpenTag = false;

        private function closeOpenTag(): void
        {
            if ($this->inOpenTag) {
                $this->buffer .= '>';
                $this->inOpenTag = false;
                if (!empty($this->stack)) {
                    $this->stack[count($this->stack) - 1]['hasContent'] = true;
                }
            }
        }

        public function openMemory(): bool
        {
            $this->buffer = '';
            $this->uri = null;
            $this->stack = [];
            $this->inOpenTag = false;
            return true;
        }

        public function openUri(string $uri): bool
        {
            $this->buffer = '';
            $this->uri = $uri;
            $this->stack = [];
            $this->inOpenTag = false;
            @file_put_contents($uri, '');
            return true;
        }

        public function setIndent(bool $enable): bool
        {
            return true;
        }

        public function setIndentString(string $indent): bool
        {
            return true;
        }

        public function startDocument(?string $version = '1.0', ?string $encoding = null, ?string $standalone = null): bool
        {
            $ver = $version ?: '1.0';
            $decl = '<?xml version="' . $ver . '"';
            if ($encoding !== null && $encoding !== '') {
                $decl .= ' encoding="' . $encoding . '"';
            }
            if ($standalone !== null && $standalone !== '') {
                $decl .= ' standalone="' . $standalone . '"';
            }
            $decl .= '?>' . "\n";
            $this->buffer .= $decl;
            return true;
        }

        public function endDocument(): bool
        {
            while (!empty($this->stack)) {
                $this->endElement();
            }
            return true;
        }

        public function startElement(string $name): bool
        {
            $this->closeOpenTag();
            if (!empty($this->stack)) {
                $this->stack[count($this->stack) - 1]['hasContent'] = true;
            }
            $this->buffer .= '<' . $name;
            $this->stack[] = ['tag' => $name, 'hasContent' => false];
            $this->inOpenTag = true;
            return true;
        }

        public function startElementNs(?string $prefix, string $name, ?string $namespace): bool
        {
            $qname = ($prefix !== null && $prefix !== '') ? ($prefix . ':' . $name) : $name;
            $this->startElement($qname);
            if ($namespace !== null && $namespace !== '') {
                $attrName = ($prefix !== null && $prefix !== '') ? ('xmlns:' . $prefix) : 'xmlns';
                $this->writeAttribute($attrName, $namespace);
            }
            return true;
        }

        public function writeAttribute(string $name, $value): bool
        {
            if (!$this->inOpenTag) {
                return false;
            }
            $escaped = htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $this->buffer .= ' ' . $name . '="' . $escaped . '"';
            return true;
        }

        public function writeAttributeNs(?string $prefix, string $name, ?string $namespace, $value): bool
        {
            $qname = ($prefix !== null && $prefix !== '') ? ($prefix . ':' . $name) : $name;
            return $this->writeAttribute($qname, $value);
        }

        public function text(string $content): bool
        {
            $this->closeOpenTag();
            $this->buffer .= htmlspecialchars($content, ENT_XML1 | ENT_COMPAT, 'UTF-8');
            return true;
        }

        public function writeRaw(string $content): bool
        {
            $this->closeOpenTag();
            $this->buffer .= $content;
            return true;
        }

        public function writeCdata(string $content): bool
        {
            $this->closeOpenTag();
            $this->buffer .= '<![CDATA[' . $content . ']]>';
            return true;
        }

        public function writeComment(string $content): bool
        {
            $this->closeOpenTag();
            $this->buffer .= '<!--' . $content . '-->';
            return true;
        }

        public function endElement(): bool
        {
            if (empty($this->stack)) {
                return false;
            }
            $top = array_pop($this->stack);
            if ($this->inOpenTag && !$top['hasContent']) {
                $this->buffer .= '/>';
                $this->inOpenTag = false;
            } else {
                $this->closeOpenTag();
                $this->buffer .= '</' . $top['tag'] . '>';
            }
            return true;
        }

        public function fullEndElement(): bool
        {
            if (empty($this->stack)) {
                return false;
            }
            $top = array_pop($this->stack);
            $this->closeOpenTag();
            $this->buffer .= '</' . $top['tag'] . '>';
            return true;
        }

        public function writeElement(string $name, ?string $content = null): bool
        {
            $this->startElement($name);
            if ($content !== null) {
                $this->text($content);
            }
            return $this->endElement();
        }

        public function writeElementNs(?string $prefix, string $name, ?string $namespace, ?string $content = null): bool
        {
            $this->startElementNs($prefix, $name, $namespace);
            if ($content !== null) {
                $this->text($content);
            }
            return $this->endElement();
        }

        public function outputMemory(bool $flush = true): string
        {
            while (!empty($this->stack)) {
                $this->endElement();
            }
            $out = $this->buffer;
            if ($flush) {
                $this->buffer = '';
            }
            return $out;
        }

        public function flush(bool $empty = true)
        {
            while (!empty($this->stack)) {
                $this->endElement();
            }
            if ($this->uri !== null && $this->uri !== '') {
                @file_put_contents($this->uri, $this->buffer, FILE_APPEND);
                if ($empty) {
                    $this->buffer = '';
                }
                return true;
            }
            $out = $this->buffer;
            if ($empty) {
                $this->buffer = '';
            }
            return $out;
        }
    }
}
