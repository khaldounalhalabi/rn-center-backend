<?php

namespace  App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<Customer>
 * @implements IBaseRepository<Customer>
 */
class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }
}
