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

    public function view($id, array $relationships = [], array $countable = []): ?Offer
    {
        $offer = parent::view($id, $relationships, $countable);

        if ($offer?->canShow()) {
            return $offer;
        }

        return null;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Offer
    {
        $offer = $this->repository->find($id);

        if (!$offer?->canUpdate()) {
            return null;
        }

        return $this->repository->update($data, $offer, $relationships, $countable);
    }

    public function delete($id): ?bool
    {
        $offer = $this->repository->find($id);

        if (!$offer?->canDelete()) {
            return null;
        }

        $offer->delete();
        return true;
    }
}
