<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Prescription\StoreUpdatePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class PrescriptionController extends ApiController
{
    private PrescriptionService $prescriptionService;

    public function __construct()
    {

        $this->prescriptionService = PrescriptionService::make();

        if (auth()->user()?->isClinic()) {
            $this->relations = ['customer.user', 'medicinesData.medicine', 'appointment'];
            $this->indexRelations = ['customer.user'];
        } else {
            $this->relations = ['clinic.user', 'customer.user', 'medicinesData.medicine'];
            $this->indexRelations = ['clinic.user', 'customer.user'];
        }
    }

    public function index()
    {
        $items = $this->prescriptionService->indexWithPagination($this->indexRelations);
        if ($items) {
            return $this->apiResponse(PrescriptionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($prescriptionId)
    {
        /** @var Prescription|null $item */
        $item = $this->prescriptionService->view($prescriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PrescriptionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData();
    }

    public function store(StoreUpdatePrescriptionRequest $request)
    {
        /** @var Prescription|null $item */
        $item = $this->prescriptionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new PrescriptionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($prescriptionId, StoreUpdatePrescriptionRequest $request)
    {
        /** @var Prescription|null $item */
        $item = $this->prescriptionService->update($request->validated(), $prescriptionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new PrescriptionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData();
    }

    public function destroy($prescriptionId)
    {
        $item = $this->prescriptionService->delete($prescriptionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->prescriptionService->export($ids);
    }

    public function getImportExample()
    {
        return $this->prescriptionService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->prescriptionService->import();
    }

    public function removeMedicine($medicineDataId)
    {
        $result = $this->prescriptionService->removeMedicine($medicineDataId);

        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }
        return $this->noData(false);
    }

    public function getAppointmentPrescriptions($appointmentId)
    {
        $data = $this->prescriptionService->getByAppointmentId($appointmentId, $this->relations);
        if ($data) {
            return $this->apiResponse(PrescriptionResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }

    public function getCustomerPrescriptions($customerId)
    {
        $data = $this->prescriptionService->getClinicCustomerPrescriptions($customerId, auth()?->user()?->getClinicId(), $this->indexRelations);
        if ($data) {
            return $this->apiResponse(PrescriptionResource::collection($data['data']), self::STATUS_OK, __('site.get_successfully'), $data['pagination_data']);
        }

        return $this->noData();
    }
}
