<?php

namespace App\Services;

use App\Models\Offer;
use App\Repositories\OfferRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Offer>
 * @property OfferRepository $repository
 */
class OfferService extends BaseService
{
    use Makable;

    protected string $repositoryClass = OfferRepository::class;
}
