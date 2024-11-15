<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
	public $fromEmail;
	public $fromName;
	public $protocol = 'smtp';
	public $SMTPHost;
	public $SMTPUser;
	public $SMTPPass;
	public $SMTPPort;
	public $SMTPCrypto;
	public $mailType = 'html';
	public $charset = 'UTF-8';
	public $wordWrap = true;

	public function __construct()
	{
		$this->fromEmail = env('email.fromEmail', 'default@domain.com');
		$this->fromName = env('email.fromName', 'Default Name');
		$this->SMTPHost = env('email.SMTPHost', 'smtp.domain.com');
		$this->SMTPUser = env('email.SMTPUser', 'user@domain.com');
		$this->SMTPPass = env('email.SMTPPass', '');
		$this->SMTPPort = env('email.SMTPPort', 587);
		$this->SMTPCrypto = env('email.SMTPCrypto', 'tls');
	}
}
