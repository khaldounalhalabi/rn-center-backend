<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\ServiceCategory\StoreUpdateServiceCategoryRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use App\Services\ServiceCategoryService;
use Illuminate\Http\Request;

class ServiceCategoryController extends ApiController
{
    private ServiceCategoryService $serviceCategoryService;

    public function __construct()
    {
        $this->serviceCategoryService = ServiceCategoryService::make();
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->serviceCategoryService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ServiceCategoryResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($serviceCategoryId)
    {
        /** @var ServiceCategory|null $item */
        $item = $this->serviceCategoryService->view($serviceCategoryId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceCategoryResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateServiceCategoryRequest $request)
    {
        /** @var ServiceCategory|null $item */
        $item = $this->serviceCategoryService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceCategoryResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($serviceCategoryId, StoreUpdateServiceCategoryRequest $request)
    {
        /** @var ServiceCategory|null $item */
        $item = $this->serviceCategoryService->update($request->validated(), $serviceCategoryId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ServiceCategoryResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($serviceCategoryId)
    {
        $item = $this->serviceCategoryService->delete($serviceCategoryId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->serviceCategoryService->export($ids);
    }

    public function getImportExample()
    {
        return $this->serviceCategoryService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->serviceCategoryService->import();
    }
}
