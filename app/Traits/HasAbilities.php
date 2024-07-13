<?php

namespace App\Traits;

/**
 * @property int clinic_id
 */
trait HasAbilities
{
    public function canShow(): bool
    {
        return
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin();
    }

    public function canUpdate(): bool
    {
        return ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin();
    }

    public function canDelete(): bool
    {
        return ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin();
    }
}
