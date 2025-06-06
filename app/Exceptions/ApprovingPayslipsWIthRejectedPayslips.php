<?php

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use Exception;

class ApprovingPayslipsWIthRejectedPayslips extends Exception
{
    public function __construct()
    {
        parent::__construct(
            trans('site.rejected_payslips_errors'),
            ApiController::REJECTED_PAYSLIP,
        );
    }
}
