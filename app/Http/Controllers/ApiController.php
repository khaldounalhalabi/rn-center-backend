<?php

namespace App\Http\Controllers;

use App\Traits\RestTrait;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
    use RestTrait;

    protected array $relations = [];

    protected array $indexRelations = [];

    protected array $countable = [];

    //Exception
    public const STATUS_BAD_REQUEST = 400;

    public const STATUS_CREATED = 201;

    public const STATUS_FORBIDDEN = 403;

    public const STATUS_NO_CONTENT = 204;

    public const STATUS_NOT_AUTHENTICATED = 402;

    public const STATUS_NOT_FOUND = 404;

    public const STATUS_OK = 200;

    public const STATUS_RESET_CONTENT = 205;

    public const STATUS_UNAUTHORIZED = 401;

    public const STATUS_VALIDATION = 405;

    public const TOKEN_EXPIRATION = 406;

    public const STATUS_INVALID_TIME_TO_BOOK = 425;
}
