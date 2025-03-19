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
        /** @var Offer $offer */
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

        $offer = $this->repository->update($data, $offer, $relationships, $countable);

        if ($offer->start_at?->lessThanOrEqualTo(now()) && $offer->end_at?->greaterThanOrEqualTo(now())) {
            $offer->update([
                'is_active' => true,
            ]);
        }

        return $offer;
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

    public function getByClinic($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getByClinicId($clinicId, $relations, $countable);
    }
}
