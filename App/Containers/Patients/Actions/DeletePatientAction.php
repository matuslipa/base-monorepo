<?php

declare(strict_types=1);

namespace App\Containers\Patients\Actions;

use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Containers\Patients\Models\Patient;
use App\Core\Parents\Actions\Action;
use Illuminate\Database\DatabaseManager;

/**
 * @package App\Containers\Patients
 */
final class DeletePatientAction extends Action
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
     *
     * @throws \Throwable
     */
    public function run(Patient $patient): void
    {
        $this->databaseManager->transaction(function () use ($patient): void {
            $this->patientsRepository->delete($patient);
        });
    }
}
