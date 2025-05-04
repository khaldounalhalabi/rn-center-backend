<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Media\AddCustomerAttachmentRequest;
use App\Http\Resources\MediaResource;
use App\Services\CustomerService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends ApiController
{
    public function delete($mediaId)
    {
        if (!auth()->user()){
            return $this->noData();
        }

        $media = Media::find($mediaId);
        if ($media) {
            $media->delete();
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }
        return $this->noData();
    }

    public function addCustomerAttachment(AddCustomerAttachmentRequest $request)
    {
        $customer = CustomerService::make()->view($request->validated('customer_id'));

        if (!$customer) {
            return $this->noData();
        }

        $media = $customer->addMedia($request->validated('attachment'))->toMediaCollection();
        return $this->apiResponse(
            new MediaResource($media),
            self::STATUS_OK,
            __('site.stored_successfully')
        );
    }
}
