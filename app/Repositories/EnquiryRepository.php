<?php

namespace App\Repositories;

use App\Models\Enquiry;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Enquiry>
 */
class EnquiryRepository extends BaseRepository
{
    protected string $modelClass = Enquiry::class;

}
