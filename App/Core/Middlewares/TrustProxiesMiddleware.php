<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use Illuminate\Http\Middleware\TrustProxies;
use Symfony\Component\HttpFoundation\Request;

final class TrustProxiesMiddleware extends TrustProxies
{
    /**
     * @inheritDoc
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO;
}
