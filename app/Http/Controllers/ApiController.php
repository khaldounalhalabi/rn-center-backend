<?php

namespace App\Http\Controllers;

use App\Traits\RestTrait;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
    use RestTrait;

    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_CREATED = 201;
    public const STATUS_FORBIDDEN = 403;

    //Exception
    public const STATUS_NOT_AUTHENTICATED = 402;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_OK = 200;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_VALIDATION = 405;
    public const STATUS_INVALID_TIME_TO_BOOK = 425;
    public const STATUS_EXPIRED_SUBSCRIPTION = 432;
    public const STATUS_UN_VERIFIED_EMAIL = 433;
    public const STATUS_MUST_AGREE_ON_CONTRACT = 435;

    protected array $relations = [];
    protected array $indexRelations = [];
    protected array $countable = [];
}
