<?php

namespace App\Services\Enquiry;

use App\Services\Contracts\IBaseService;
use App\Models\Enquiry;

/**
 * @extends IBaseService<Enquiry>
 * Interface IUserService
 */
interface IEnquiryService extends IBaseService
{
    /**
     * @param       $enquiryId
     * @param array $data
     * @return bool
     */
    public function reply($enquiryId, array $data): bool;
}
