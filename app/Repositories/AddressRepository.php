<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Address>
 * @implements IBaseRepository<Address>
 */
class AddressRepository extends BaseRepository
{
    public function __construct(Address $address)
    {
        parent::__construct($address);
    }
}
