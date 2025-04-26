<?php

namespace App\Repositories;

use App\Models\Medicine;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Medicine>
 */
class MedicineRepository extends BaseRepository
{
    protected string $modelClass = Medicine::class;

    public function getByName(string $name): ?Medicine
    {
        return $this->globalQuery()
            ->where('name', $name)
            ->first();
    }

    public function getByBarcode(string $barcode): ?Medicine
    {
        return $this->globalQuery()
            ->where('barcode', $barcode)
            ->first();
    }
}
