<?php

namespace App\Services\Offer;

use App\Models\Offer;
use App\Repositories\OfferRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IOfferService<Offer>
 * @extends BaseService<Offer>
 */
class OfferService extends BaseService implements IOfferService
{
    /**
     * OfferService constructor.
     * @param OfferRepository $repository
     */
    public function __construct(OfferRepository $repository)
    {
        parent::__construct($repository);
    }
}
