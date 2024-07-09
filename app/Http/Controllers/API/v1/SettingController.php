<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Setting\StoreUpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Services\SettingService;

class SettingController extends ApiController
{
    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = SettingService::make();

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->settingService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(SettingResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function update($settingId, StoreUpdateSettingRequest $request)
    {
        /** @var Setting|null $item */
        $item = $this->settingService->update($request->validated(), $settingId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SettingResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }
}
