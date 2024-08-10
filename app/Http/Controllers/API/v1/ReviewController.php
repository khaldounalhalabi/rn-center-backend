<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Review\StoreUpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;

class ReviewController extends ApiController
{
    private ReviewService $reviewService;

    public function __construct()
    {
        $this->reviewService = ReviewService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function store(StoreUpdateReviewRequest $request)
    {
        /** @var Review|null $item */
        $item = $this->reviewService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ReviewResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($reviewId, StoreUpdateReviewRequest $request)
    {
        /** @var Review|null $item */
        $item = $this->reviewService->update($request->validated(), $reviewId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ReviewResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($reviewId)
    {
        $review = $this->reviewService->view($reviewId);
        if (!$review?->canDelete()) {
            return $this->noData();
        }

        $item = $this->reviewService->delete($reviewId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function getByClinic($clinicId)
    {
        $data = $this->reviewService->getByClinic($clinicId, $this->relations, $this->countable);
        if ($data) {
            return $this->apiResponse(ReviewResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }
        return $this->noData();
    }
}
