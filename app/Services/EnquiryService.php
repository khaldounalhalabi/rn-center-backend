<?php

namespace App\Services;

use App\Mail\EnquiryReplyEmail;
use App\Models\Enquiry;
use App\Repositories\EnquiryRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * @extends BaseService<Enquiry>
 * @property EnquiryRepository $repository
 */
class EnquiryService extends BaseService
{
    use Makable;

    protected string $repositoryClass = EnquiryRepository::class;

    public function view($id, array $relationships = [], array $countable = []): ?Enquiry
    {
        /** @var Enquiry $enquiry */
        $enquiry = parent::view($id, $relationships, $countable);

        if (!$enquiry->read_at) {
            $enquiry->read_at = now();
            $enquiry->save();
        }

        return $enquiry;
    }

    /**
     * @param       $enquiryId
     * @param array $data
     * @return bool
     */
    public function reply($enquiryId, array $data): bool
    {
        try {
            $enquiry = $this->repository->find($enquiryId);

            if (!$enquiry) {
                return false;
            }

            Mail::to($enquiry->email)->send(new EnquiryReplyEmail($data));

            return true;
        } catch (Exception $exception) {
            Log::error("Exception Happened While Replying To An Enquiry : " . $exception->getMessage());
            return false;
        }
    }
}
