<?php

namespace App\Http\Resources;

use App\Enums\MediaTypeEnum;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** @mixin Media
 * @property mixed $id
 */
class MediaResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'model_id'   => $this->model_id,
            'model_type' => MediaTypeEnum::getType($this->model_type),
            'file_name'  => $this->file_name,
            'file_type'  => $this->mime_type,
            'file_url'   => $this->original_url,
            'size'       => $this->size,
            'collection' => $this->collection_name,
        ];
    }
}
