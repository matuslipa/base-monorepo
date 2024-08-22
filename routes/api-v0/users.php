<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Resource: Users
Route::apiResource('users', \App\Containers\Users\Controllers\UsersApiController::class)
    ->only(['index', 'store', 'update', 'destroy', 'show'])
    ->parameters([
        'users' => 'userId',
    ]);
