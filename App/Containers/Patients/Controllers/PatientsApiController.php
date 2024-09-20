<?php

declare(strict_types=1);

namespace App\Containers\Patients\Controllers;

use App\Containers\Patients\Actions\CreatePatientAction;
use App\Containers\Patients\Actions\DeletePatientAction;
use App\Containers\Patients\Actions\GetAllPatientsAction;
use App\Containers\Patients\Actions\GetPatientAction;
use App\Containers\Patients\Actions\UpdatePatientAction;
use App\Containers\Patients\Requests\PatientRequestFilter;
use App\Containers\Patients\Transformers\PatientApiTransformer;
use App\Core\Responses\ApiResponse;
use Illuminate\Http\Request;

/**
 * @package App\Containers\Patients
 */
final class PatientsApiController
{
    public function __construct(
        private readonly PatientApiTransformer $patientApiTransformer
    ) {
    }

    /**
     * GET: Get collection of Patients.
     *
     * @param \App\Containers\Patients\Actions\GetAllPatientsAction $getAllAction
     *
     * @return \App\Core\Responses\ApiResponse
     */
    public function index(GetAllPatientsAction $getAllAction): ApiResponse
    {
        return new ApiResponse([
            'data' => $this->patientApiTransformer->runTransformation($getAllAction->run()),
        ]);
    }

    /**
     * GET: Get single Patient.
     *
     * @param \App\Containers\Patients\Actions\GetPatientAction $getAction
     * @param int|string $patientId
     *
     * @return \App\Core\Responses\ApiResponse
     */
    public function show(GetPatientAction $getAction, int | string $patientId): ApiResponse
    {
        return new ApiResponse([
            'data' => $this->patientApiTransformer->runTransformation($getAction->run((int) $patientId)),
        ]);
    }

    /**
     * @param \App\Containers\Patients\Requests\PatientRequestFilter $requestFilter
     * @param \App\Containers\Patients\Actions\CreatePatientAction $createAction
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(
        PatientRequestFilter $requestFilter,
        CreatePatientAction $createAction,
        Request $request
    ): ApiResponse {
        $patient = $createAction->run($requestFilter->getValidatedData($request));
        return new ApiResponse([
            'data' => $this->patientApiTransformer->runTransformation($patient),
        ], 201);
    }

    /**
     * @param \App\Containers\Patients\Requests\PatientRequestFilter $requestFilter
     * @param \App\Containers\Patients\Actions\GetPatientAction $getAction
     * @param \App\Containers\Patients\Actions\UpdatePatientAction $updateAction
     * @param \Illuminate\Http\Request $request
     * @param int|string $patientId
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function update(
        PatientRequestFilter $requestFilter,
        GetPatientAction $getAction,
        UpdatePatientAction $updateAction,
        Request $request,
        int | string $patientId
    ): ApiResponse {
        $patient = $getAction->run((int) $patientId);

        $patient = $updateAction->run($patient, $requestFilter->getValidatedData($request, $patient));

        return new ApiResponse([
            'data' => $this->patientApiTransformer->runTransformation($patient),
        ]);
    }

    /**
     * DELETE: Delete Patient.
     *
     * @param \App\Containers\Patients\Actions\GetPatientAction $getAction
     * @param \App\Containers\Patients\Actions\DeletePatientAction $deleteAction ,
     * @param int|string $patientId
     *
     * @return \App\Core\Responses\ApiResponse
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    public function destroy(
        GetPatientAction $getAction,
        DeletePatientAction $deleteAction,
        int | string $patientId
    ): ApiResponse {
        $patient = $getAction->run((int) $patientId);

        $deleteAction->run($patient);

        return new ApiResponse([], 204);
    }
}
