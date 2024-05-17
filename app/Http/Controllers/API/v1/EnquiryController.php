<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Enquiry\EnquiryReplyRequest;
use App\Http\Requests\Enquiry\StoreUpdateEnquiryRequest;
use App\Http\Resources\EnquiryResource;
use App\Models\Enquiry;
use App\Services\Enquiry\IEnquiryService;

class EnquiryController extends ApiController
{
    private IEnquiryService $enquiryService;

    public function __construct(IEnquiryService $enquiryService)
    {

        $this->enquiryService = $enquiryService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->enquiryService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(EnquiryResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($enquiryId)
    {
        /** @var Enquiry|null $item */
        $item = $this->enquiryService->view($enquiryId, $this->relations);
        if ($item) {
            return $this->apiResponse(new EnquiryResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateEnquiryRequest $request)
    {
        /** @var Enquiry|null $item */
        $item = $this->enquiryService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new EnquiryResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($enquiryId, StoreUpdateEnquiryRequest $request)
    {
        /** @var Enquiry|null $item */
        $item = $this->enquiryService->update($request->validated(), $enquiryId, $this->relations);
        if ($item) {
            return $this->apiResponse(new EnquiryResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }


    public function reply($enquiryId, EnquiryReplyRequest $request)
    {
        $result = $this->enquiryService->reply($enquiryId, $request->validated());

        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.success'));
        }

        return $this->noData(false);
    }
}
