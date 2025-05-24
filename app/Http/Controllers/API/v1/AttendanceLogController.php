<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\AttendanceLog\EditOrCreateAttendanceLogRequest;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use Illuminate\Http\Request;

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

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'mimes:xlsx'],
        ]);

        $this->service->import();

        return $this->apiResponse(['message' => "temp message here"], self::STATUS_OK, __('site.success'));
    }
}
