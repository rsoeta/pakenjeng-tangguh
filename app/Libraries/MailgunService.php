<?php

namespace App\Libraries;

use Mailgun\Mailgun;

class MailgunService
{
    protected $mg;
    protected $domain;

    public function __construct()
    {
        // API Key dan Domain Mailgun
        $this->mg = Mailgun::create('d4da39df94a58dddea4dc979031c07df-79295dd0-71537e74'); // API Key dari Mailgun
        $this->domain = 'sandbox4062bb992e764847a089be7415ca32ad.mailgun.org';           // Domain Mailgun (sandbox atau custom)
    }

    public function sendEmail($to, $subject, $htmlContent)
    {
        $this->mg->messages()->send($this->domain, [
            'from'    => 'postmaster@sandbox4062bb992e764847a089be7415ca32ad.mailgun.org',    // Alamat email pengirim
            'to'      => $to,
            'subject' => $subject,
            'html'    => $htmlContent
        ]);
    }
}
