<?php

namespace App\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<Address>
 * @property AddressRepository $repository
 */
class AddressService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AddressRepository::class;
}
