<?php

namespace App\Services\Enquiry;

use App\Mail\EnquiryReplyEmail;
use App\Models\Enquiry;
use App\Repositories\EnquiryRepository;
use App\Services\Contracts\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * @implements IEnquiryService<Enquiry>
 * @extends BaseService<Enquiry>
 */
class EnquiryService extends BaseService implements IEnquiryService
{
    /**
     * EnquiryService constructor.
     * @param EnquiryRepository $repository
     */
    public function __construct(EnquiryRepository $repository)
    {
        parent::__construct($repository);
    }

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
