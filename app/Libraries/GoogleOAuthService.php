<?php

namespace App\Libraries;

use Config\GoogleOAuth;

class GoogleOAuthService
{
    protected GoogleOAuth $config;

    public function __construct(?GoogleOAuth $config = null)
    {
        $this->config = $config ?? config(GoogleOAuth::class);
    }

    public function isConfigured(): bool
    {
        return $this->config->isConfigured();
    }

    public function getRedirectUri(): string
    {
        return $this->config->getRedirectUri();
    }

    public function generateState(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate a PKCE S256 verifier/challenge pair.
     *
     * @return array{verifier: string, challenge: string}
     */
    public function generatePkcePair(): array
    {
        $verifier  = $this->base64UrlEncode(random_bytes(32));
        $challenge = $this->base64UrlEncode(hash('sha256', $verifier, true));

        return [
            'verifier'  => $verifier,
            'challenge' => $challenge,
        ];
    }

    public function buildAuthorizationUrl(string $state, string $codeChallenge): string
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Google OAuth credentials are not configured.');
        }

        $params = [
            'client_id'             => $this->config->clientId,
            'redirect_uri'          => $this->getRedirectUri(),
            'response_type'         => 'code',
            'scope'                 => $this->config->scopes,
            'state'                 => $state,
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => 'S256',
            'access_type'           => 'online',
        ];

        if ($this->config->prompt !== '') {
            $params['prompt'] = $this->config->prompt;
        }

        if ($this->config->hostedDomain !== '') {
            $params['hd'] = $this->config->hostedDomain;
        }

        return $this->config->authEndpoint . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Exchange authorization code for OAuth tokens and validate ID token claims when present.
     *
     * @return array<string, mixed>
     */
    public function exchangeCodeForToken(string $code, string $codeVerifier): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Google OAuth credentials are not configured.');
        }

        $payload = http_build_query([
            'code'          => $code,
            'client_id'     => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'redirect_uri'  => $this->getRedirectUri(),
            'grant_type'    => 'authorization_code',
            'code_verifier' => $codeVerifier,
        ], '', '&', PHP_QUERY_RFC3986);

        $response = $this->sendHttpRequest(
            'POST',
            $this->config->tokenEndpoint,
            [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            $payload
        );

        $data = json_decode($response['body'], true);

        if ($response['status'] !== 200 || !is_array($data)) {
            $errorDetail = is_array($data)
                ? (string) ($data['error_description'] ?? $data['error'] ?? 'Invalid response')
                : 'Invalid JSON response';
            throw new \RuntimeException('Google token exchange failed (HTTP ' . $response['status'] . '): ' . $errorDetail);
        }

        if (empty($data['access_token']) || !is_string($data['access_token'])) {
            throw new \RuntimeException('Google token response missing access_token.');
        }

        if (isset($data['id_token']) && is_string($data['id_token']) && $data['id_token'] !== '') {
            $data['id_token_claims'] = $this->validateIdTokenClaims($data['id_token']);
        }

        return $data;
    }

    /**
     * Fetch normalized user profile from Google OpenID Connect UserInfo endpoint.
     *
     * @return array{sub: string, email: string, email_verified: bool, name: string, picture: string}
     */
    public function fetchUserInfo(string $accessToken): array
    {
        $response = $this->sendHttpRequest(
            'GET',
            $this->config->userInfoEndpoint,
            [
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json',
            ]
        );

        $data = json_decode($response['body'], true);

        if ($response['status'] !== 200 || !is_array($data)) {
            $errorDetail = is_array($data)
                ? (string) ($data['error_description'] ?? $data['error'] ?? 'Invalid response')
                : 'Invalid JSON response';
            throw new \RuntimeException('Google userinfo request failed (HTTP ' . $response['status'] . '): ' . $errorDetail);
        }

        return [
            'sub'            => (string) ($data['sub'] ?? ''),
            'email'          => strtolower(trim((string) ($data['email'] ?? ''))),
            'email_verified' => $this->isTruthyClaim($data['email_verified'] ?? false),
            'name'           => (string) ($data['name'] ?? ''),
            'picture'        => (string) ($data['picture'] ?? ''),
        ];
    }

    /**
     * Complete OAuth callback verification: token exchange, userinfo fetch, and cross-claim checks.
     *
     * @return array{sub: string, email: string, email_verified: bool, name: string, picture: string}
     */
    public function authenticateCallback(string $code, string $codeVerifier): array
    {
        $tokenData = $this->exchangeCodeForToken($code, $codeVerifier);
        $userInfo  = $this->fetchUserInfo($tokenData['access_token']);

        if (isset($tokenData['id_token_claims']) && is_array($tokenData['id_token_claims'])) {
            $claims = $tokenData['id_token_claims'];

            if (isset($claims['email']) && is_string($claims['email']) && trim($claims['email']) !== '') {
                $tokenEmail = strtolower(trim($claims['email']));
                if ($userInfo['email'] === '' || $tokenEmail !== $userInfo['email']) {
                    throw new \RuntimeException('Google ID token email does not match userinfo email.');
                }
            }

            if (array_key_exists('email_verified', $claims) && !$this->isTruthyClaim($claims['email_verified'])) {
                throw new \RuntimeException('Google ID token email is not verified.');
            }

            if (
                isset($claims['sub'])
                && is_string($claims['sub'])
                && $claims['sub'] !== ''
                && $userInfo['sub'] !== ''
                && $claims['sub'] !== $userInfo['sub']
            ) {
                throw new \RuntimeException('Google ID token subject does not match userinfo subject.');
            }
        }

        if ($this->config->hostedDomain !== '') {
            $expectedHd = strtolower(trim($this->config->hostedDomain));
            $atPos      = strrpos($userInfo['email'], '@');
            $emailHd    = $atPos !== false ? strtolower(substr($userInfo['email'], $atPos + 1)) : '';
            if ($emailHd !== $expectedHd) {
                throw new \RuntimeException('Google account domain does not match required hosted domain.');
            }
        }

        return $userInfo;
    }

    /**
     * Execute an HTTPS request using native PHP cURL.
     *
     * @param string[] $headers
     *
     * @return array{status: int, body: string}
     */
    protected function sendHttpRequest(string $method, string $url, array $headers = [], ?string $body = null): array
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new \RuntimeException('Failed to initialize cURL session.');
        }

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => $this->config->httpTimeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
        ];

        $upperMethod = strtoupper($method);
        if ($upperMethod === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = $body ?? '';
        } elseif ($upperMethod !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = $upperMethod;
            if ($body !== null) {
                $options[CURLOPT_POSTFIELDS] = $body;
            }
        }

        curl_setopt_array($ch, $options);

        $responseBody = curl_exec($ch);
        $curlErrno    = curl_errno($ch);
        $curlError    = curl_error($ch);
        $httpStatus   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErrno !== 0 || $responseBody === false) {
            throw new \RuntimeException('cURL request error (' . $curlErrno . '): ' . $curlError);
        }

        return [
            'status' => $httpStatus,
            'body'   => (string) $responseBody,
        ];
    }

    /**
     * Decode and validate core claims from Google's signed JWT id_token received over direct TLS.
     *
     * @return array<string, mixed>
     */
    protected function validateIdTokenClaims(string $idToken): array
    {
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid Google ID token structure.');
        }

        $payloadJson = $this->base64UrlDecode($parts[1]);
        $claims      = json_decode($payloadJson, true);

        if (!is_array($claims)) {
            throw new \RuntimeException('Invalid Google ID token payload.');
        }

        $aud = $claims['aud'] ?? '';
        if (!is_string($aud) || $aud !== $this->config->clientId) {
            throw new \RuntimeException('Google ID token audience mismatch.');
        }

        $iss = $claims['iss'] ?? '';
        if (!in_array($iss, ['https://accounts.google.com', 'accounts.google.com'], true)) {
            throw new \RuntimeException('Google ID token issuer mismatch.');
        }

        $exp = $claims['exp'] ?? null;
        if (!is_numeric($exp) || ((int) $exp + 60) < time()) {
            throw new \RuntimeException('Google ID token has expired.');
        }

        if ($this->config->hostedDomain !== '') {
            $expectedHd = strtolower(trim($this->config->hostedDomain));
            $tokenHd    = strtolower(trim((string) ($claims['hd'] ?? '')));
            if ($tokenHd !== $expectedHd) {
                throw new \RuntimeException('Google ID token hosted domain mismatch.');
            }
        }

        return $claims;
    }

    protected function isTruthyClaim(mixed $value): bool
    {
        return in_array($value, [true, 1, 'true', '1'], true);
    }

    protected function base64UrlEncode(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $encoded): string
    {
        $remainder = strlen($encoded) % 4;
        if ($remainder !== 0) {
            $encoded .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \RuntimeException('Failed to decode base64url segment.');
        }

        return $decoded;
    }
}
