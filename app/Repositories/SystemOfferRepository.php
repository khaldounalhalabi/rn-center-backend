<?php

namespace App\Repositories;

use App\Models\SystemOffer;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<SystemOffer>
 */
class SystemOfferRepository extends BaseRepository
{
    protected string $modelClass = SystemOffer::class;
}
