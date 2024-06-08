<?php

namespace App\Services;

use App\Models\PhoneNumber;
use App\Repositories\PhoneNumberRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<PhoneNumber>
 * @property PhoneNumberRepository $repository
 */
class PhoneNumberService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PhoneNumberRepository::class;
}
