<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Setting\StoreUpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
    private SettingService $settingService;

    public function __construct()
    {
        $this->settingService = SettingService::make();

        // place the relations you want to return them within the response
        $this->relations = ['media'];
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

    public function show($settingId)
    {
        $item = $this->settingService->view($settingId, $this->relations);
        if ($item) {
            return $this->apiResponse(new SettingResource($item), self::STATUS_OK, __('site.get_successfully'));
        }
        return $this->noData();
    }

    public function getByLabel($label)
    {
        $item = $this->settingService->getByLabel($label);
        if ($item) {
            return $this->apiResponse(new SettingResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function getByLabels(Request $request)
    {
        $labels = $request->input('labels') ?? [];
        $settings = SettingRepository::make()->globalQuery()->whereIn('label' , $labels)->get();
        return $this->apiResponse(SettingResource::collection($settings) , self::STATUS_OK, __('site.get_successfully'));
    }
}
