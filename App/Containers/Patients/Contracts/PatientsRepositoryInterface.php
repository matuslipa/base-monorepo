<?php

declare(strict_types=1);

namespace App\Containers\Patients\Contracts;

use App\Containers\Patients\Models\Patient;
use Illuminate\Support\Collection;

/**
 * @package App\Containers\Patients
 */
interface PatientsRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \App\Containers\Patients\Models\Patient
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id): Patient;

    /**
     * @return \Illuminate\Support\Collection<\App\Containers\Patients\Models\Patient>
     */
    public function getAll(): Collection;

    /**
     * @param mixed[] $data
     *
     * @return \App\Containers\Patients\Models\Patient
     */
    public function create(array $data): Patient;

    /**
     * @param \App\Containers\Patients\Models\Patient $patient
     */
    public function save(Patient $patient): void;

    /**
     * @param \App\Containers\Patients\Models\Patient $patient
     */
    public function delete(Patient $patient): void;

    /**
     * @return \App\Containers\Patients\Contracts\PatientsQueryInterface
     */
    public function query(): PatientsQueryInterface;
}
