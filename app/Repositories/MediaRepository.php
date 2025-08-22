<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @extends  BaseRepository<Media>
 */
class MediaRepository extends BaseRepository
{
    protected string $modelClass = Media::class;
}
