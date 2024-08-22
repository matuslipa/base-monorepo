<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', static function (
    \Illuminate\Contracts\View\Factory $view,
): \Illuminate\Contracts\View\View {
    return $view->make('homepage');
});
