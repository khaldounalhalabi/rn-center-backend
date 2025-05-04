<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\FormulaVariable\StoreUpdateFormulaVariableRequest;
use App\Http\Resources\v1\FormulaVariableResource;
use App\Models\FormulaVariable;
use App\Services\v1\FormulaVariable\FormulaVariableService;
use Illuminate\Http\Request;

class FormulaVariableController extends ApiController
{
    private FormulaVariableService $formulaVariableService;

    public function __construct()
    {
        $this->formulaVariableService = FormulaVariableService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->formulaVariableService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(FormulaVariableResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($formulaVariableId)
    {
        /** @var FormulaVariable|null $item */
        $item = $this->formulaVariableService->view($formulaVariableId, $this->relations);
        if ($item) {
            return $this->apiResponse(new FormulaVariableResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }
}
