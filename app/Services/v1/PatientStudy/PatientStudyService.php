<?php

namespace App\Services\v1\PatientStudy;

use App\Models\PatientStudy;
use App\Modules\Compressor\Zip;
use App\Modules\DICOM\DicomModeValidator;
use App\Repositories\PatientStudyRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
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
     * @param UploadedFile|UploadedFile[] $file
     * @return UploadedFile|null
     */
    private function isZipFile(UploadedFile|array $file): ?UploadedFile
    {
        $file = Arr::wrap($file);
        foreach ($file as $item) {
            if ($item->getMimeType() == "application/zip") {
                return $item;
            }
        }

        return null;
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
            if ($this->isZipFile($data['dicom_files'])) {
                file_put_contents($storePath, $this->isZipFile($data['dicom_files'])->get());
            } else {
                Zip::compress($data['dicom_files'], $storePath);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/octet-stream'
            ])->withBody(
                file_get_contents($storePath),
                'application/octet-stream'
            )->post(URL::format(config('orthanc.server_url'), '/instances'));

            if (!$response->successful()) {
                if (file_exists($storePath)) {
                    unlink($storePath);
                }
                throw new Exception("Failed to store study");
            }
            $instanceData = $response->json();

            if ($this->isArrayOfArrays($instanceData)) {
                foreach ($instanceData as $key => $instanceDataItem) {
                    try {
                        $this->createCustomerStudy($instanceDataItem, $storePath, $data['customer_id'], "{$data['title']} - (" . $key + 1 . ")");
                        break;
                    } catch (Exception|Throwable $exception) {
                        continue;
                    }
                }
            } else {
                $this->createCustomerStudy($instanceData, $storePath, $data['customer_id'], $data['title']);
            }
            unlink($storePath);
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
        try {
            $studyDateTime = Carbon::createFromFormat('Ymd His', "$studyDate $studyTime");
        } catch (Exception) {
            $studyDateTime = now();
        }

        $studySeries = $this->getStudySeries($studyData['ID']);

        $availableModes = DicomModeValidator::extractModalitiesAndViewModes($studySeries, $studyData);

        $this->repository->create([
            'uuid' => $instanceData['ID'],
            'customer_id' => $customerId,
            'patient_uuid' => $instanceData['ParentPatient'],
            'study_uuid' => $instanceData['ParentStudy'],
            'study_uid' => $studyInstanceUID,
            'study_date' => $studyDateTime->format('Y-m-d H:i:s'),
            'title' => $title,
            'available_modes' => $availableModes,
        ]);
    }

    /**
     * @param string $studyId
     * @return array
     */
    private function getStudySeries(string $studyId): array
    {
        $response = Http::get(URL::format(config('orthanc.server_url'), "/studies/$studyId/series"));
        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }
}
