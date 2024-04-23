<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\Service\StoreUpdateServiceRequest;
use App\Services\Service\IServiceService;
use App\Http\Resources\ServiceResource;
use App\Http\Controllers\ApiController;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends ApiController
{
    private IServiceService $serviceService;

    public function __construct(IServiceService $serviceService)
    {

        $this->serviceService = $serviceService;

        // place the relations you want to return them within the response
        $this->relations = ['serviceCategory' , 'clinic'] ;
    }

    public function index()
    {
        $items = $this->serviceService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ServiceResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($serviceId)
    {
        /** @var Service|null $item */
        $item = $this->serviceService->view($serviceId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateServiceRequest $request)
    {
        /** @var Service|null $item */
        $item = $this->serviceService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($serviceId, StoreUpdateServiceRequest $request)
    {
        /** @var Service|null $item */
        $item = $this->serviceService->update($request->validated(), $serviceId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($serviceId)
    {
        $item = $this->serviceService->delete($serviceId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->serviceService->export($ids);
    }

    public function getImportExample()
    {
        return $this->serviceService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->serviceService->import();
    }
}
