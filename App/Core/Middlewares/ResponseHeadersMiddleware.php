<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Services\ResponseManager\ResponseManager;
use Closure;

final class ResponseHeadersMiddleware
{
    /**
     * @param \App\Core\Services\ResponseManager\ResponseManager $responseManager
     */
    public function __construct(
        private readonly ResponseManager $responseManager
    ) {
    }

    /**
     * Manage API response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $this->responseManager->handle($request, $response);

        return $response;
    }
}
