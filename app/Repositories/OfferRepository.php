<?php

namespace App\Repositories;

use App\Models\Offer;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Offer>
 */
class OfferRepository extends BaseRepository
{
    protected string $modelClass = Offer::class;
}
