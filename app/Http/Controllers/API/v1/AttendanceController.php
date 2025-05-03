<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\v1\AttendanceResource;
use App\Services\v1\Attendance\AttendanceService;

/**
 * @extends ApiController<AttendanceService>
 */
class AttendanceController extends ApiController
{
    private AttendanceService $service;

    public function __construct()
    {
        $this->service = AttendanceService::make();
        $this->relations = [];
    }

    public function markAsApproved()
    {
        $attendance = $this->service->markAsApproved();
        if ($attendance) {
            return $this->apiResponse(AttendanceResource::make($attendance), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function getPending()
    {
        $data = $this->service->getPending($this->relations, [
            'onTimeLogs', 'overtimeLogs', 'lateLogs', 'attendanceLogs', 'users'
        ]);

        if ($data) {
            return $this->apiResponse(AttendanceResource::collection($data['data'])->detailed(), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
