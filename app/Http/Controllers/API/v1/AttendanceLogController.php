<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\AttendanceLog\EditOrCreateAttendanceLogRequest;
use App\Http\Resources\v1\AttendanceLogResource;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @extends ApiController<AttendanceLogService>
 */
class AttendanceLogController extends ApiController
{
    private AttendanceLogService $service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->service = AttendanceLogService::make();
            return $next($request);
        });
        $this->relations = [];
    }

    public function editOrCreate($userId, EditOrCreateAttendanceLogRequest $request)
    {
        $result = $this->service->editOrCreate($userId, $request->validated());

        if ($result) {
            return $this->apiResponse($result, self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->noData();
    }

    public function getImportExample()
    {
        return $this->service->getImportExample();
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->service->export($ids);
    }

    public function exportMine()
    {
        return $this->service->exportMine();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'mimes:xlsx'],
        ]);

        $this->service->import();

        return $this->apiResponse(['message' => "temp message here"], self::STATUS_OK, __('site.success'));
    }

    public function myAttendance(Request $request)
    {
        $validator = Validator::make([
            'year' => $request->query('year'),
            'month' => $request->query('month'),
        ], [
            'year' => 'numeric|min:2000|max:3000',
            'month' => 'numeric|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(
                $this->service->myAttendanceLogs(),
                self::STATUS_OK,
                trans('site.get_successfully')
            );
        }

        return $this->apiResponse(
            $this->service->myAttendanceLogs($request->query('year'), $request->query('month')),
            self::STATUS_OK,
            trans('site.get_successfully')
        );
    }

    public function checkin()
    {
        $result = $this->service->checkin();
        if ($result) {
            return $this->apiResponse(
                AttendanceLogResource::make($result),
                self::STATUS_OK,
                trans('site.success')
            );
        }

        return $this->noData();
    }

    public function checkout()
    {
        $result = $this->service->checkout();
        if ($result) {
            return $this->apiResponse(
                AttendanceLogResource::make($result),
                self::STATUS_OK,
                trans('site.success')
            );
        }

        return $this->noData();
    }

    public function latestLog()
    {
        $log = $this->service->latestLogToday();

        if ($log) {
            return $this->apiResponse(
                AttendanceLogResource::make($log),
                self::STATUS_OK,
                trans('site.get_successfully')
            );
        }

        return $this->noData();
    }

    public function myStatistics()
    {
        return $this->apiResponse(
            $this->service->attendanceStatisticsByUser(user()->id),
            self::STATUS_OK,
            trans('site.get_successfully')
        );
    }
}
