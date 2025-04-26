<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Medicine\StoreUpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Services\MedicineService;
use Illuminate\Http\Request;

class MedicineController extends ApiController
{
    private MedicineService $medicineService;

    public function __construct()
    {
        $this->medicineService = MedicineService::make();
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->medicineService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(MedicineResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($medicineId)
    {
        /** @var Medicine|null $item */
        $item = $this->medicineService->view($medicineId, $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicineResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateMedicineRequest $request)
    {
        /** @var Medicine|null $item */
        $item = $this->medicineService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicineResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($medicineId, StoreUpdateMedicineRequest $request)
    {
        /** @var Medicine|null $item */
        $item = $this->medicineService->update($request->validated(), $medicineId, $this->relations);
        if ($item) {
            return $this->apiResponse(new MedicineResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($medicineId)
    {
        $item = $this->medicineService->delete($medicineId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->medicineService->export($ids);
    }

    public function getImportExample()
    {
        return $this->medicineService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->medicineService->import();
    }
}
