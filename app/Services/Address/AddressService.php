<?php

namespace App\Services\Address;

use App\Models\Address;
use App\Services\Contracts\BaseService;
use App\Repositories\AddressRepository;

/**
 * @implements IAddressService<Address>
 * Class UserService
 */
class AddressService extends BaseService implements IAddressService
{
    /**
     * AddressService constructor.
     * @param AddressRepository $repository
     */
    public function __construct(AddressRepository $repository)
    {
        parent::__construct($repository);
    }
}
