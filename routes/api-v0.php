<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| API v0 Routes
|--------------------------------------------------------------------------
*/
\Illuminate\Support\Facades\Route::name('api.')->prefix('v0')->group(
    static function (): void {
        $path = base_path('routes/api-v0');
        $files = \Illuminate\Support\Facades\File::files($path);

        foreach ($files as $file) {
            /** @noinspection PhpIncludeInspection */
            require $file;
        }
    }
);
