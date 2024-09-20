<?php

declare(strict_types=1);

namespace App\Containers\Patients\Actions;

use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Containers\Patients\Models\Patient;
use App\Core\Parents\Actions\Action;

/**
 * @package App\Containers\Patients
 */
final class GetPatientAction extends Action
{
    /**
     * @param \App\Containers\Patients\Contracts\PatientsRepositoryInterface $patientsRepository
     */
    public function __construct(
        private readonly PatientsRepositoryInterface $patientsRepository
    ) {
    }

    /**
     * @param int $id
     *
     * @return \App\Containers\Patients\Models\Patient
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function run(int $id): Patient
    {
        return $this->patientsRepository->get($id);
    }
}
