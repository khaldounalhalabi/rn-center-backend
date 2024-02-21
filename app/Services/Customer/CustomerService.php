<?php

namespace App\Services\Customer;

use App\Repositories\CustomerRepository;
use App\Services\Contracts\BaseService;

/**
 * @implements ICustomerService
 * Class UserService
 */
class CustomerService extends BaseService implements ICustomerService
{
    /**
     * CustomerService constructor.
     *
     * @param CustomerRepository $repository
     */
    public function __construct(CustomerRepository $repository)
    {
        parent::__construct($repository);
    }
}
