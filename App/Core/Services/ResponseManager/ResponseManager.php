<?php

declare(strict_types=1);

namespace App\Core\Services\ResponseManager;

use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Core\Services\ResponseManager
 */
final class ResponseManager
{
    /**
     * @var bool
     */
    private bool $isPreflight = false;

    /**
     * @var mixed[]
     */
    private array $runtimeHeaders = [];

    /**
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    private array $cookies = [];

    /**
     * @var string[]
     */
    private array $forgetCookies = [];

    /**
     * Initialize response manager.
     *
     * @param \Illuminate\Config\Repository $configRepository
     */
    public function __construct(
        private readonly Config $configRepository
    ) {
    }

    /**
     * Handle response and append required headers.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function handle(Request $request, Response $response): void
    {
        $this->isPreflight = $request->isMethod('OPTIONS');
        $this->appendHeaders($response);
        $this->handleCookies($response);

        if ($this->isPreflight) {
            $response->setStatusCode(200);
            $response->setContent(null);
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null): mixed
    {
        return $this->configRepository->get("response_headers.${key}", $default);
    }

    /**
     * @param mixed[] $headers
     */
    public function addHeaders(array $headers): void
    {
        $this->runtimeHeaders = \array_merge_recursive($this->runtimeHeaders, $headers);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Cookie $cookie
     */
    public function addCookie(Cookie $cookie): void
    {
        $this->cookies[] = $cookie;
    }

    /**
     * @param string $cookie
     */
    public function forgetCookie(string $cookie): void
    {
        $this->forgetCookies[] = $cookie;
    }

    /**
     * Append headers to given response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function appendHeaders(Response $response): void
    {
        foreach ($this->runtimeHeaders as $header => $value) {
            $response->headers->set($header, $value, $header !== 'Link');
        }

        // X-Frame-Options //
        $xFrameOptions = \strtolower($this->getConfig('x_frame_options'));
        if ($xFrameOptions !== 'allow') {
            if ($xFrameOptions !== 'deny' && $xFrameOptions !== 'sameorigin') {
                $xFrameOptions = "allow-from ${xFrameOptions}";
            }

            $response->headers->set('X-Frame-Options', $xFrameOptions);
        }

        // X-Content-Type-Options //
        if ($this->getConfig('x_content_type_options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // HTTP Strict Transport Security //
        if ($this->getConfig('hsts.enabled')) {
            $response->headers->set(
                'Strict-Transport-Security',
                $this->getHSTSHeader(
                    (int) $this->getConfig('hsts.max_age'),
                    (bool) $this->getConfig('hsts.include_subdomains')
                )
            );
        }

        // CORS //
        if ($this->getConfig('cors.enabled')) {
            $this->appendCorsHeaders($response);
        }
    }

    /**
     * Set CORS headers to the response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function appendCorsHeaders(Response $response): void
    {
        if ($this->getConfig('cors.allow_credentials')) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');

        if ($this->isPreflight) {
            $allowHeaders = $this->getConfig('cors.allow_headers');
            if (\is_array($allowHeaders) && \count($allowHeaders)) {
                $response->headers->set('Access-Control-Allow-Headers', \implode(', ', $allowHeaders));
            }

            $maxAge = $this->getConfig('cors.max_age');
            if ($maxAge !== null) {
                $response->headers->set('Access-Control-Max-Age', (string) $maxAge);
            }
        } else {
            $exposeHeaders = $this->getConfig('cors.expose_headers');
            if (\is_array($exposeHeaders) && \count($exposeHeaders)) {
                $response->headers->set('Access-Control-Expose-Headers', \implode(', ', $exposeHeaders));
            }
        }
    }

    /**
     * Set cookies to the response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function handleCookies(Response $response): void
    {
        foreach ($this->cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        foreach ($this->forgetCookies as $cookie) {
            $response->headers->clearCookie($cookie);
        }
    }

    /**
     * Get HTTP Strict Transport Security header.
     *
     * @param int $maxAge
     * @param bool $includeSubDomains
     *
     * @return string
     */
    private function getHSTSHeader(int $maxAge, bool $includeSubDomains): string
    {
        $value = "max-age={$maxAge};";
        if ($includeSubDomains) {
            $value .= ' includeSubDomains;';
        }

        return $value;
    }
}
