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
final class UpdatePatientAction extends Action
{
    /**
     * @param \App\Containers\Patients\Contracts\PatientsRepositoryInterface $patientsRepository
     * @param \Illuminate\Database\DatabaseManager $databaseManager
     */
    public function __construct(
        private readonly PatientsRepositoryInterface $patientsRepository,
        private readonly DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param \App\Containers\Patients\Models\Patient $patient
     * @param \App\Containers\Patients\Values\InputData\PatientInputData $data
     *
     * @return \App\Containers\Patients\Models\Patient
     *
     * @throws \Throwable
     */
    public function run(Patient $patient, PatientInputData $data): Patient
    {
        return $this->databaseManager->transaction(function () use ($patient, $data): Patient {
            $patient->compactFill($data->getAttributes());
            $this->patientsRepository->save($patient);

            return $patient;
        });
    }
}
