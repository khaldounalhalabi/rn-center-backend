<?php

namespace  App\Repositories;

use App\Models\Enquiry;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Enquiry>
 */
class EnquiryRepository extends BaseRepository
{
    public function __construct(Enquiry $enquiry)
    {
        parent::__construct($enquiry);
    }
}
