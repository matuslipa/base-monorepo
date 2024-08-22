<?php

declare(strict_types=1);

namespace App\Core\Services\Factories;

use App\Core\Exceptions\GeneralException;
use App\Core\Helpers\ContainerHelper;
use App\Core\Parents\Exceptions\Exception;
use App\Core\Values\Enums\ResponseTypeEnum;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class GeneralExceptionFactory
{
    /**
     * @param \Throwable $exception
     *
     * @return \App\Core\Parents\Exceptions\Exception
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(\Throwable $exception): Exception
    {
        $normalizedException = new GeneralException(
            $exception->getMessage(),
            $exception,
            $exception->getCode()
        );

        if ($exception instanceof HttpExceptionInterface) {
            $normalizedException->setStatusCode($exception->getStatusCode());
            $normalizedException->setResponseHeaders($exception->getHeaders());
        }

        $normalizedException->setResponseType(match ($normalizedException->getStatusCode()) {
            403 => ResponseTypeEnum::UNAUTHORIZED(),
            404 => ResponseTypeEnum::NOT_FOUND(),
            405 => ResponseTypeEnum::METHOD_NOT_ALLOWED(),
            500 => ResponseTypeEnum::GENERAL(),
            default => ResponseTypeEnum::UNKNOWN(),
        });

        // prevent outputting some sensitive data in production
        if ($normalizedException->getStatusCode() === 500 &&
            ContainerHelper::make(Application::class)->environment('production')
        ) {
            $normalizedException->setMessage('exceptions.server_error');
        }

        return $normalizedException;
    }
}
