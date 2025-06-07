<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\Vacation\StoreUpdateVacationRequest;
use App\Http\Resources\v1\VacationResource;
use App\Models\Vacation;
use App\Services\v1\Vacation\VacationService;

class VacationController extends ApiController
{
    private VacationService $vacationService;

    public function __construct()
    {
        $this->vacationService = VacationService::make();
        if (isAdmin() || isSecretary()) {
            $this->relations = ['user'];
        }
    }

    public function index()
    {
        $items = $this->vacationService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(VacationResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($vacationId)
    {
        /** @var Vacation|null $item */
        $item = $this->vacationService->view($vacationId, $this->relations);
        if ($item) {
            return $this->apiResponse(new VacationResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateVacationRequest $request)
    {
        /** @var Vacation|null $item */
        $item = $this->vacationService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new VacationResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('site.something_went_wrong'));
    }

    public function update($vacationId, StoreUpdateVacationRequest $request)
    {
        /** @var Vacation|null $item */
        $item = $this->vacationService->update($request->validated(), $vacationId, $this->relations);
        if ($item) {
            return $this->apiResponse(new VacationResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($vacationId)
    {
        $item = $this->vacationService->delete($vacationId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function byUser($userId)
    {
        $data = $this->vacationService->byUser($userId, [], $this->countable);

        if ($data) {
            return $this->apiResponse(
                VacationResource::collection($data['data']),
                self::STATUS_OK,
                trans('site.get_successfully'),
                $data['pagination_data']
            );
        }

        return $this->noData([]);
    }
}
