<?php

namespace App\Services\v1\PatientStudy;

use App\Models\PatientStudy;
use App\Modules\Compressor\Zip;
use App\Repositories\PatientStudyRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

/**
 * @extends BaseService<PatientStudy>
 * @property PatientStudyRepository $repository
 */
class PatientStudyService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PatientStudyRepository::class;

    private function isArrayOfArrays(array $input): bool
    {
        foreach ($input as $value) {
            if (!is_array($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws Throwable
     */
    public function addStudyToCustomer(array $data): bool
    {
        DB::beginTransaction();
        try {
            $uuid = Str::uuid();
            $fileName = "$uuid.zip";
            $storePath = storage_path("app/private/{$data['customer_id']}/$fileName");
            $zipPath = Zip::compress($data['dicom_files'], storage_path("app/private/{$data['customer_id']}/$uuid.zip"));
            $response = Http::withHeaders([
                'Content-Type' => 'application/octet-stream'
            ])->withBody(
                file_get_contents($storePath),
                'application/octet-stream'
            )->post(URL::format(config('orthanc.server_url'), '/instances'));

            if (!$response->successful()) {
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                throw new Exception("Failed to store study");
            }
            $instanceData = $response->json();

            if ($this->isArrayOfArrays($instanceData)) {
                foreach ($instanceData as $key => $instanceDataItem) {
                    $this->createCustomerStudy($instanceDataItem, $zipPath, $data['customer_id'], "{$data['title']} - (" . $key + 1 . ")");
                }
            } else {
                $this->createCustomerStudy($instanceData, $zipPath, $data['customer_id'], $data['title']);
            }

            DB::commit();
            return true;

        } catch (Exception $exception) {
            DB::rollBack();
            if (app()->environment('local')) {
                throw $exception;
            }
            return false;
        }
    }

    /**
     * @param mixed       $instanceData
     * @param string|null $zipPath
     * @param int         $customerId
     * @param string      $title
     * @return void
     * @throws Exception
     */
    private function createCustomerStudy(mixed $instanceData, ?string $zipPath, int $customerId, string $title): void
    {
        $studyUUID = $instanceData['ParentStudy'] ?? null;
        if (!$studyUUID) {
            throw new Exception("Undefined Study");
        }

        $response = HTTP::get(URL::format(config('orthanc.server_url'), "/studies/$studyUUID"));
        if (!$response->successful()) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            throw new Exception("Failed to get study instance UID");
        }

        $studyData = $response->json();
        $studyInstanceUID = $studyData['MainDicomTags']['StudyInstanceUID'] ?? null;
        if (!$studyInstanceUID) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            throw new Exception("Failed to get study instance UID");
        }

        $studyDate = $studyData['MainDicomTags']['StudyDate'];
        $studyTime = $studyData['MainDicomTags']['StudyTime'];
        $studyDateTime = Carbon::createFromFormat('Ymd His', "$studyDate $studyTime");


        $this->repository->create([
            'uuid' => $instanceData['ID'],
            'customer_id' => $customerId,
            'patient_uuid' => $instanceData['ParentPatient'],
            'study_uuid' => $instanceData['ParentStudy'],
            'study_uid' => $studyInstanceUID,
            'study_date' => $studyDateTime->format('Y-m-d H:i:s'),
            'title' => $title
        ]);
    }
}
