<?php

declare(strict_types=1);

namespace App\Containers\Patients\Actions;

use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Containers\Patients\Models\Patient;
use App\Containers\Patients\Values\InputData\PatientInputData;
use App\Core\Parents\Actions\Action;
use Illuminate\Database\DatabaseManager;

/**
 * @package App\Containers\Patients
 */
final class CreatePatientAction extends Action
{
    /**
     * @param \App\Containers\Patients\Contracts\PatientsRepositoryInterface $patientsRepository
     * @param \Illuminate\Database\DatabaseManager $databaseManager ,
     */
    public function __construct(
        private readonly PatientsRepositoryInterface $patientsRepository,
        private readonly DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param \App\Containers\Patients\Values\InputData\PatientInputData $data
     *
     * @return \App\Containers\Patients\Models\Patient
     *
     * @throws \Throwable
     */
    public function run(PatientInputData $data): Patient
    {
        return $this->databaseManager->transaction(function () use ($data): Patient {
            return $this->patientsRepository->create($data->getAttributes());
        });
    }
}
