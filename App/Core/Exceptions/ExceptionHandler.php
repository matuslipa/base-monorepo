<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Helpers\ContainerHelper;
use App\Core\Parents\Exceptions\Exception;
use App\Core\Responses\ApiResponse;
use App\Core\Services\Factories\GeneralExceptionFactory;
use App\Core\Services\Factories\ValidationExceptionFactory;
use App\Core\Values\Translation;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class ExceptionHandler
{
    /**
     * @param \Throwable $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(\Throwable $exception): Response
    {
        if ($exception instanceof ForceRedirectException) {
            return ContainerHelper::make(Redirector::class)->to(
                $exception->getUrl(),
                $exception->getStatusCode(),
                [],
                true
            );
        }

        $exception = $this->normalizeException($exception);
        $exceptionUuid = Str::uuid()->toString();

        try {
            $translator = ContainerHelper::make(Translator::class);
        } catch (BindingResolutionException) {
            $translator = null;
        }

        $data = [
            'type' => $exception->getResponseType()->getValue(),
            'message' => $exception->toLocalizedString($translator),
            'id' => $exceptionUuid,
        ];

        if ($exception->hasErrors()) {
            $data['errors'] = [];

            foreach ($exception->getErrors() as $field => $message) {
                $message = $message instanceof Translation
                    ? $message->toString($translator)
                    : $translator?->get($message);

                $data['errors'][] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }

        if (ContainerHelper::make(Application::class)->environment() !== 'production') {
            $tempException = $exception;
            $stacks = [$tempException->getTrace()];

            while ($tempException->getPrevious()) {
                $tempException = $tempException->getPrevious();
                $stacks[] = $tempException->getTrace();
            }

            $data['stack'] = $stacks;
        }

        return new ApiResponse(
            $data,
            $exception->getStatusCode(),
            $exception->getResponseHeaders()
        );
    }

    /**
     * @param \Throwable $exception
     *
     * @return \App\Core\Parents\Exceptions\Exception
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function normalizeException(\Throwable $exception): Exception
    {
        return match ($exception::class) {
            Exception::class => $exception,
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class => ContainerHelper::make(ValidationExceptionFactory::class)->createFromIlluminateException(
                $exception,
            ),
            default => ContainerHelper::make(GeneralExceptionFactory::class)->create($exception),
        };
    }
}
