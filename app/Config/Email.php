<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
	public string $fromEmail  = 'no-reply@sinden.pasirlangu.desa.id';
	public string $fromName   = 'SINDEN System';
	public string $recipients = '';

	// Gunakan SMTP
	public string $protocol   = 'smtp';
	public string $SMTPHost   = 'sinden.pasirlangu.desa.id';
	public string $SMTPUser   = 'no-reply@sinden.pasirlangu.desa.id';
	public string $SMTPPass   = 'rdSZj!XKU^kn}G99'; // <— ubah sesuai password akun email
	public int    $SMTPPort   = 465; // 465=SSL, 587=TLS
	public string $SMTPCrypto = 'ssl'; // ubah ke 'tls' jika pakai port 587

	public string $mailType   = 'html';
	public bool   $validate   = true;
	public string $charset    = 'utf-8';
	public string $newline    = "\r\n";
}
