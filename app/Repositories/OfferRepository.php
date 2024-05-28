<?php

namespace  App\Repositories;

use App\Models\Offer;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Offer>
 */
class OfferRepository extends BaseRepository
{
    public function __construct(Offer $offer)
    {
        parent::__construct($offer);
    }
}
