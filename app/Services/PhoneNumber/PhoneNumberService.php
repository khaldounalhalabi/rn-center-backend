<?php

namespace App\Services\PhoneNumber;

use App\Models\PhoneNumber;
use App\Repositories\PhoneNumberRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements IPhoneNumberService<PhoneNumber>
 * Class UserService
 */
class PhoneNumberService extends BaseService implements IPhoneNumberService
{
    /**
     * PhoneNumberService constructor.
     * @param PhoneNumberRepository $repository
     */
    public function __construct(PhoneNumberRepository $repository)
    {
        parent::__construct($repository);
    }
}
