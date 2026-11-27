<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Global Security Headers & Context-Aware CSP Filter.
 *
 * Applies defense-in-depth HTTP headers (HSTS, XFO, XCTO, Referrer-Policy,
 * COOP, CORP, Permissions-Policy, CSP) while preserving full compatibility
 * across public strict routes, auth routes, and legacy internal dashboards.
 */
class SecurityHeaders implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No pre-request action needed
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $isHttps = $this->isHttpsRequest($request);

        // HSTS: strictly HTTPS-only per RFC 6797 (never emitted over plain HTTP/localhost)
        if ($isHttps && !$response->hasHeader('Strict-Transport-Security')) {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Core clickjacking, MIME-sniffing, referrer, and cross-origin isolation headers
        if (!$response->hasHeader('X-Frame-Options')) {
            $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }

        if (!$response->hasHeader('X-Content-Type-Options')) {
            $response->setHeader('X-Content-Type-Options', 'nosniff');
        }

        if (!$response->hasHeader('Referrer-Policy')) {
            $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        if (!$response->hasHeader('Cross-Origin-Opener-Policy')) {
            $response->setHeader('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        }

        if (!$response->hasHeader('Cross-Origin-Resource-Policy')) {
            $response->setHeader('Cross-Origin-Resource-Policy', 'same-origin');
        }

        if (!$response->hasHeader('Permissions-Policy')) {
            $response->setHeader(
                'Permissions-Policy',
                'geolocation=(self), camera=(self), microphone=(), payment=(), usb=()'
            );
        }

        // Modern OWASP guidance disables legacy XSS auditor (CSP supersedes it cleanly)
        $response->setHeader('X-XSS-Protection', '0');

        // Hide PHP version disclosure if emitted by SAPI
        $response->removeHeader('X-Powered-By');
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        // Context-aware Content-Security-Policy (unless already explicitly set by endpoint)
        if (!$response->hasHeader('Content-Security-Policy')) {
            $path = $this->normalizePath($request);
            $csp  = $this->buildCspForPath($path, $isHttps);
            $response->setHeader('Content-Security-Policy', $csp);
        }

        return $response;
    }

    private function isHttpsRequest(RequestInterface $request): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        if ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443) {
            return true;
        }

        if (strtolower((string) $request->getHeaderLine('X-Forwarded-Proto')) === 'https') {
            return true;
        }

        $cfVisitor = strtolower((string) $request->getHeaderLine('CF-Visitor'));
        if ($cfVisitor !== '' && str_contains($cfVisitor, '"scheme":"https"')) {
            return true;
        }

        return false;
    }

    private function normalizePath(RequestInterface $request): string
    {
        $path = trim((string) $request->getUri()->getPath(), '/');
        $path = preg_replace('#^index\.php/?#i', '', $path) ?? $path;

        return trim($path, '/');
    }

    private function buildCspForPath(string $path, bool $isHttps): string
    {
        $upgrade = $isHttps ? '; upgrade-insecure-requests' : '';

        // Profile 1: Strict public root & lockscreen (ZERO 'unsafe-inline', ZERO 'unsafe-eval')
        if ($path === '' || $path === 'lockscreen') {
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data:",
                "connect-src 'self'",
                "manifest-src 'self'",
                "worker-src 'self'",
                "frame-src 'none'",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "form-action 'self'",
            ]) . $upgrade;
        }

        // Profile 2: Public article reader (no inline scripts; 'unsafe-inline' style-src preserved for rich article HTML)
        if ($path === 'article' || str_starts_with($path, 'article/')) {
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' https://fonts.googleapis.com 'unsafe-inline'",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data: https:",
                "connect-src 'self'",
                "manifest-src 'self'",
                "worker-src 'self'",
                "frame-src 'none'",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "form-action 'self'",
            ]) . $upgrade;
        }

        // Profile 3: Auth pages (no inline scripts; Turnstile + Bootstrap 5 + SweetAlert2 styling compatible)
        if (in_array($path, ['login', 'register', 'lupa-password', 'requestReset', 'reset-password'], true)) {
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self' https://challenges.cloudflare.com https://cdn.jsdelivr.net",
                "style-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net 'unsafe-inline'",
                "font-src 'self' https://fonts.gstatic.com data:",
                "img-src 'self' data: https:",
                "connect-src 'self' https://challenges.cloudflare.com https://cdn.jsdelivr.net",
                "manifest-src 'self'",
                "worker-src 'self'",
                "frame-src 'self' https://challenges.cloudflare.com",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "form-action 'self'",
            ]) . $upgrade;
        }

        // Profile 4: Legacy internal authenticated dashboards (full CDN + inline compatibility + clickjacking/base protection)
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net https://code.jquery.com https://unpkg.com https://ajax.googleapis.com https://code.highcharts.com https://kit.fontawesome.com https://challenges.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net https://unpkg.com https://code.ionicframework.com https://use.fontawesome.com",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://use.fontawesome.com https://code.ionicframework.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' blob: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net https://unpkg.com https://server.arcgisonline.com https://challenges.cloudflare.com https://*.tile.openstreetmap.org",
            "frame-src 'self' blob: data: https://challenges.cloudflare.com https://www.google.com",
            "worker-src 'self' blob:",
            "child-src 'self' blob:",
            "manifest-src 'self'",
            "media-src 'self' blob: data:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "form-action 'self'",
        ]) . $upgrade;
    }
}
