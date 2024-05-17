<?php

namespace App\Services\Enquiry;

use App\Models\Enquiry;
use App\Repositories\EnquiryRepository;
use App\Services\Contracts\BaseService;

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
}
