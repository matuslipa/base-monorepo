<?php

declare(strict_types=1);

namespace App\Containers\Users\Controllers;

use App\Containers\Users\Actions\CreateUserAction;
use App\Containers\Users\Actions\DeleteUserAction;
use App\Containers\Users\Actions\GetAllUsersAction;
use App\Containers\Users\Actions\GetUserAction;
use App\Containers\Users\Actions\UpdateUserAction;
use App\Containers\Users\Requests\UserRequestFilter;
use App\Containers\Users\Transformers\UserApiTransformer;
use App\Core\Responses\ApiResponse;
use Illuminate\Http\Request;

/**
 * @package App\Containers\Users
 */
final readonly class UsersApiController
{
    public function __construct(
        private UserApiTransformer $userApiTransformer
    ) {
    }

    /**
     * GET: Get collection of Users.
     *
     * @param \App\Containers\Users\Actions\GetAllUsersAction $getAllAction
     *
     * @return \App\Core\Responses\ApiResponse
     */
    public function index(GetAllUsersAction $getAllAction): ApiResponse
    {
        return new ApiResponse([
            'data' => $this->userApiTransformer->runTransformation($getAllAction->run()),
        ]);
    }

    /**
     * GET: Get single User.
     *
     * @param \App\Containers\Users\Actions\GetUserAction $getAction
     * @param int|string $userId
     *
     * @return \App\Core\Responses\ApiResponse
     */
    public function show(GetUserAction $getAction, int | string $userId): ApiResponse
    {
        return new ApiResponse([
            'data' => $this->userApiTransformer->runTransformation($getAction->run((int) $userId)),
        ]);
    }

    /**
     * POST: Store new User.
     *
     * @param \App\Containers\Users\Requests\UserRequestFilter $requestFilter
     * @param \App\Containers\Users\Actions\CreateUserAction $createAction
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(
        UserRequestFilter $requestFilter,
        CreateUserAction $createAction,
        Request $request
    ): ApiResponse {
        $data = $requestFilter->getValidatedData($request);
        $user = $createAction->run($data);

        return new ApiResponse([
            'data' => $this->userApiTransformer->runTransformation($user),
        ]);
    }

    /**
     * PUT/PATCH: Update User.
     *
     * @param \App\Containers\Users\Requests\UserRequestFilter $requestFilter
     * @param \App\Containers\Users\Actions\GetUserAction $getAction,
     * @param \App\Containers\Users\Actions\UpdateUserAction $updateAction
     * @param \Illuminate\Http\Request $request
     * @param int|string $userId
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Throwable
     */
    public function update(
        UserRequestFilter $requestFilter,
        GetUserAction $getAction,
        UpdateUserAction $updateAction,
        Request $request,
        int | string $userId
    ): ApiResponse {
        $user = $getAction->run((int) $userId);

        $data = $requestFilter->getValidatedData($request, $user);
        $user = $updateAction->run($user, $data);

        return new ApiResponse([
            'data' => $this->userApiTransformer->runTransformation($user),
        ]);
    }

    /**
     * DELETE: Delete User.
     *
     * @param \App\Containers\Users\Actions\GetUserAction $getAction
     * @param \App\Containers\Users\Actions\DeleteUserAction $deleteAction,
     * @param int|string $userId
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Throwable
     */
    public function destroy(
        GetUserAction $getAction,
        DeleteUserAction $deleteAction,
        int | string $userId
    ): ApiResponse {
        $user = $getAction->run((int) $userId);

        $deleteAction->run($user);

        return new ApiResponse([], 204);
    }
}
