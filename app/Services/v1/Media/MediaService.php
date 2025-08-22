<?php

namespace App\Services\v1\Media;


use App\Models\Customer;
use App\Repositories\MediaRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService extends BaseService
{
    use Makable;

    protected string $repositoryClass = MediaRepository::class;

    public function getCustomerAttachments(int $customerId): ?array
    {
        $data = Media::where('model_id', $customerId)
            ->where('model_type', Customer::class)
            ->simplePaginate(request('per_page', 10));

        if ($data->count()) {
            return [
                'data' => $data->items(),
                'pagination_data' => $this
            ];
        }

        return null;
    }
}
