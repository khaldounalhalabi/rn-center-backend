<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Formula\StoreUpdateFormulaRequest;
use App\Http\Resources\v1\FormulaResource;
use App\Models\Formula;
use App\Services\v1\Formula\FormulaService;
use Illuminate\Http\Request;

class FormulaController extends ApiController
{
    private FormulaService $formulaService;

    public function __construct()
    {
        $this->formulaService = FormulaService::make();
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->formulaService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(FormulaResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($formulaId)
    {
        /** @var Formula|null $item */
        $item = $this->formulaService->view($formulaId, $this->relations);
        if ($item) {
            return $this->apiResponse(new FormulaResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateFormulaRequest $request)
    {
        /** @var Formula|null $item */
        $item = $this->formulaService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new FormulaResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($formulaId, StoreUpdateFormulaRequest $request)
    {
        /** @var Formula|null $item */
        $item = $this->formulaService->update($request->validated(), $formulaId, $this->relations);
        if ($item) {
            return $this->apiResponse(new FormulaResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($formulaId)
    {
        $item = $this->formulaService->delete($formulaId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }
}
