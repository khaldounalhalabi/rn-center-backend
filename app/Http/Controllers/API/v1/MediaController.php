<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends ApiController
{
    public function delete($mediaId)
    {
        $media = Media::find($mediaId);
        if ($media) {
            $media->delete();
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }
        return $this->noData();
    }
}
