<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class GoogleOAuth extends BaseConfig
{
    public string $clientId = '';
    public string $clientSecret = '';
    public string $redirectUri = '';
    public string $scopes = 'openid email profile';
    public string $prompt = 'select_account';
    public string $hostedDomain = '';
    public int $httpTimeout = 15;

    public string $authEndpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
    public string $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    public string $userInfoEndpoint = 'https://openidconnect.googleapis.com/v1/userinfo';

    public function __construct()
    {
        parent::__construct();

        $envClientId = env('GOOGLE_CLIENT_ID');
        if (is_string($envClientId) && trim($envClientId) !== '') {
            $this->clientId = trim($envClientId);
        } else {
            $this->clientId = trim($this->clientId);
        }

        $envClientSecret = env('GOOGLE_CLIENT_SECRET');
        if (is_string($envClientSecret) && trim($envClientSecret) !== '') {
            $this->clientSecret = trim($envClientSecret);
        } else {
            $this->clientSecret = trim($this->clientSecret);
        }

        $envRedirectUri = env('GOOGLE_REDIRECT_URI');
        if (is_string($envRedirectUri) && trim($envRedirectUri) !== '') {
            $this->redirectUri = trim($envRedirectUri);
        } else {
            $this->redirectUri = trim($this->redirectUri);
        }

        $envHostedDomain = env('GOOGLE_HOSTED_DOMAIN');
        if (is_string($envHostedDomain) && trim($envHostedDomain) !== '') {
            $this->hostedDomain = trim($envHostedDomain);
        } else {
            $this->hostedDomain = trim($this->hostedDomain);
        }
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri !== ''
            ? $this->redirectUri
            : base_url('auth/google/callback');
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }
}
