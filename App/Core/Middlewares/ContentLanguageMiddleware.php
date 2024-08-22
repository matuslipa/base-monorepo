<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;

final class ContentLanguageMiddleware
{
    public function __construct(
        private readonly Translator $translator,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next): mixed
    {
        if ($request->header('X-Web-Language') === null) {
            return $next($request);
        }

        $language = $request->header('X-Web-Language');

        if ($language !== $this->translator->getLocale()) {
            if (\is_array($language)) {
                $language = Arr::first($language);
            }

            if (\is_string($language)) {
                $this->translator->setLocale($language);
            }
        }

        return $next($request);
    }
}
