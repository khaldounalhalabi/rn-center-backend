<?php

namespace App\Traits;

/**
 * @property int clinic_id
 */
trait HasClinic
{
    public function canShow(): bool
    {
        return $this->clinic_id == auth()?->user()?->getClinicId()
            || auth()->user()?->isAdmin();
    }

    public function canUpdate(): bool
    {
        return $this->clinic_id == auth()?->user()?->getClinicId()
            || auth()->user()?->isAdmin();
    }

    public function canDelete(): bool
    {
        return $this->clinic_id == auth()?->user()?->getClinicId()
            || auth()->user()?->isAdmin();
    }
}
