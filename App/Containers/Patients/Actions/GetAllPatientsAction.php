<?php

declare(strict_types=1);

namespace App\Containers\Patients\Actions;

use App\Containers\Patients\Contracts\PatientsQueryInterface;
use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Core\Parents\Actions\Action;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Patients
 */
final class GetAllPatientsAction extends Action
{
    /**
     * @param \App\Containers\Patients\Contracts\PatientsRepositoryInterface $patientsRepository
     */
    public function __construct(
        private readonly PatientsRepositoryInterface $patientsRepository,
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Containers\Patients\Models\Patient>
     */
    public function run(): Collection
    {
        return $this->query()->getAll();
    }

    /**
     * @return \App\Containers\Patients\Contracts\PatientsQueryInterface
     */
    public function query(): PatientsQueryInterface
    {
        return $this->patientsRepository->query()->whereIsActive();
    }
}
