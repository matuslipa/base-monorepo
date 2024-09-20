<?php

declare(strict_types=1);

namespace App\Containers\Patients\Repositories;

use App\Containers\Patients\Contracts\PatientsQueryInterface;
use App\Containers\Patients\Contracts\PatientsRepositoryInterface;
use App\Containers\Patients\Models\Patient;
use App\Containers\Patients\Queries\PatientsQueryBuilder;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Patients
 */
final class PatientsRepository implements PatientsRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function get(int $id): Patient
    {
        /** @var \App\Containers\Patients\Models\Patient $patient */
        $patient = $this->query()->getById($id);
        return $patient;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return $this->query()->getAll();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Patient
    {
        $patient = new Patient();
        $patient->compactFill($data);
        $this->save($patient);

        return $patient;
    }

    /**
     * @inheritDoc
     */
    public function save(Patient $patient): void
    {
        $patient->save();
    }

    /**
     * @inheritDoc
     */
    public function delete(Patient $patient): void
    {
        $patient->delete();
    }

    /**
     * @inheritDoc
     */
    public function query(): PatientsQueryInterface
    {
        return new PatientsQueryBuilder(new Patient());
    }
}
