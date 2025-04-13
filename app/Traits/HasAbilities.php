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
            ($this->clinic_id == clinic()?->id)
            || isAdmin();
    }

    public function canUpdate(): bool
    {
        return ($this->clinic_id == clinic()?->id)
            || isAdmin();
    }

    public function canDelete(): bool
    {
        return ($this->clinic_id == clinic()?->id)
            || isAdmin();
    }
}
