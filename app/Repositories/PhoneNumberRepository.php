<?php

namespace  App\Repositories;

use App\Models\PhoneNumber;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<PhoneNumber>
 * @implements IBaseRepository<PhoneNumber>
 */
class PhoneNumberRepository extends BaseRepository
{
    public function __construct(PhoneNumber $phoneNumber)
    {
        parent::__construct($phoneNumber);
    }
}
