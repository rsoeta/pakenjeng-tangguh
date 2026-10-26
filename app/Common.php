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
