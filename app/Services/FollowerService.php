<?php

namespace App\Services;

use App\Models\Follower;
use App\Repositories\ClinicRepository;
use App\Repositories\FollowerRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends FollowerService<Follower>
 * @property FollowerRepository $repository
 */
class FollowerService extends BaseService
{
    use Makable;

    protected string $repositoryClass = FollowerRepository::class;

    public function toggleFollow($clinicId): ?string
    {
        $clinic = ClinicRepository::make()->find($clinicId);

        if (!$clinic?->isAvailable()) {
            return null;
        }

        $isFollowed = $this->repository->getByClinicAndCustomer(auth()->user()?->customer?->id, $clinicId);

        if ($isFollowed) {
            $isFollowed->delete();
            return "un-followed";
        } else {
            $this->repository->create([
                'customer_id' => auth()->user()?->customer?->id,
                'clinic_id'   => $clinicId,
            ]);
            return "followed";
        }
    }

    public function getByCustomer($customerId, array $relations = [], array $countable = [])
    {
        return $this->repository->getByCustomer($customerId, $relations, $countable);
    }
}
