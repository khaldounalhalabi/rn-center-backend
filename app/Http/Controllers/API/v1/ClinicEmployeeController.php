<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ClinicEmployee\StoreUpdateClinicEmployeeRequest;
use App\Http\Requests\UpdateClinicEmployeePermissionsRequest;
use App\Http\Resources\ClinicEmployeeResource;
use App\Models\ClinicEmployee;
use App\Services\ClinicEmployee\ClinicEmployeeService;
use Illuminate\Http\Request;

class ClinicEmployeeController extends ApiController
{
    private ClinicEmployeeService $clinicEmployeeService;

    public function __construct()
    {
        $this->clinicEmployeeService = ClinicEmployeeService::make();
        // place the relations you want to return them within the response
        $this->relations = [
            'clinic',
            'user',
            'clinic.user',
            'user.address',
            'user.address.city',
            'user.media',
            'user.roles',
            'user.permissions',
            'user.phones'
        ];
    }

    public function index()
    {
        $items = $this->clinicEmployeeService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(ClinicEmployeeResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($clinicEmployeeId)
    {
        /** @var ClinicEmployee|null $item */
        $item = $this->clinicEmployeeService->view($clinicEmployeeId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicEmployeeResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateClinicEmployeeRequest $request)
    {
        /** @var ClinicEmployee|null $item */
        $item = $this->clinicEmployeeService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicEmployeeResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($clinicEmployeeId, StoreUpdateClinicEmployeeRequest $request)
    {
        /** @var ClinicEmployee|null $item */
        $item = $this->clinicEmployeeService->update($request->validated(), $clinicEmployeeId, $this->relations);
        if ($item) {
            return $this->apiResponse(new ClinicEmployeeResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($clinicEmployeeId)
    {
        $item = $this->clinicEmployeeService->delete($clinicEmployeeId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->clinicEmployeeService->export($ids);
    }

    public function getImportExample()
    {
        return $this->clinicEmployeeService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->clinicEmployeeService->import();
    }

    public function updateEmployeePermissions(UpdateClinicEmployeePermissionsRequest $request, $clinicEmployeeId)
    {
        $clinicEmployee = $this->clinicEmployeeService->updateEmployeePermissions($clinicEmployeeId, $request->validated(), $this->relations, $this->countable);
        if ($clinicEmployee) {
            return $this->apiResponse(new ClinicEmployeeResource($clinicEmployee), self::STATUS_OK, __('site.success'));
        }
        return $this->noData();
    }
}
