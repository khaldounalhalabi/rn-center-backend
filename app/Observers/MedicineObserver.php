<?php

namespace App\Observers;

use App\Enums\MedicineStatusEnum;
use App\Models\Medicine;

class MedicineObserver
{
    public function creating(Medicine $medicine): void
    {
        if ($medicine->quantity > 0) {
            $medicine->status = MedicineStatusEnum::EXISTS->value;
        } else {
            $medicine->status = MedicineStatusEnum::OUT_OF_STOCK->value;
        }
    }

    public function updating(Medicine $medicine): void
    {
        if ($medicine->quantity > 0) {
            $medicine->status = MedicineStatusEnum::EXISTS->value;
        } else {
            $medicine->status = MedicineStatusEnum::OUT_OF_STOCK->value;
        }
    }

    /**
     * Handle the Medicine "created" event.
     */
    public function created(Medicine $medicine): void
    {
        //
    }

    /**
     * Handle the Medicine "updated" event.
     */
    public function updated(Medicine $medicine): void
    {
        //
    }

    /**
     * Handle the Medicine "deleted" event.
     */
    public function deleted(Medicine $medicine): void
    {
        //
    }

    /**
     * Handle the Medicine "restored" event.
     */
    public function restored(Medicine $medicine): void
    {
        //
    }

    /**
     * Handle the Medicine "force deleted" event.
     */
    public function forceDeleted(Medicine $medicine): void
    {
        //
    }
}
