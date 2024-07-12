<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Contracts\BaseRepository;


/**
 * @extends  BaseRepository<Address>
 * <Address>
 */
class AddressRepository extends BaseRepository
{
    protected string $modelClass = Address::class;
}
