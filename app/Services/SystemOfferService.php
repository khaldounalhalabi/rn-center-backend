<?php

namespace App\Services;

use App\Models\SystemOffer;
use App\Repositories\SystemOfferRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<SystemOffer>
 * @property SystemOfferRepository $repository
 */
class SystemOfferService extends BaseService
{
    use Makable;

    protected string $repositoryClass = SystemOfferRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        /** @var SystemOffer $offer */
        $offer = parent::store($data);
        if (isset($data['clinics']) && count($data['clinics'])) {
            $offer->clinics()->sync($data['clinics']);
        }
        return $offer->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var SystemOffer $offer */
        $offer = parent::update($data, $id);

        if (!$offer) {
            return null;
        }

        if (isset($data['clinics']) && count($data['clinics'])) {
            $offer->clinics()->sync($data['clinics']);
        }

        return $offer->load($relationships)->loadCount($countable);
    }

    public function getByClinic($clinicId, array $relations = [], array $countable = [], int $perPage = 10): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $countable);
    }

    /**
     * @param       $id
     * @param array $relationships
     * @param array $countable
     * @return SystemOffer|null
     */
    public function view($id, array $relationships = [], array $countable = []): ?SystemOffer
    {
        /** @var SystemOffer $offer */
        $offer = parent::view($id, $relationships, $countable);

        if (!auth()?->user()?->isAdmin() && !$offer?->isActive()) {
            return null;
        }

        return $offer;
    }
}
