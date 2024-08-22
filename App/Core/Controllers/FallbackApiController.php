<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Containers\Web\Services\WebFallbackHandlers\WebFallbackHandler;
use App\Core\Parents\Controllers\ApiController;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

final class FallbackApiController extends ApiController
{
    /**
     * @param \App\Containers\Web\Services\WebFallbackHandlers\WebFallbackHandler $fallbackHandler
     * @param string $url
     *
     * @return Response|View
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function show(WebFallbackHandler $fallbackHandler, string $url): View|Response
    {
        return $fallbackHandler->handle($url);
    }
}
