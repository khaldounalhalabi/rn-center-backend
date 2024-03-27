<?php

namespace Tests\Feature;

use App\Http\Resources\ClinicHolidayResource;
use App\Models\ClinicHoliday;
use Tests\Contracts\MainTestCase;

class ClinicHolidayTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = ClinicHoliday::class;

    /** @var class-string */
    protected string $resource = ClinicHolidayResource::class;

    // define the actor
    protected string $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.clinic.holidays.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = ['clinic'];

    public function test_user_can_index_ClinicHoliday()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_ClinicHoliday()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_ClinicHoliday()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_delete_a_ClinicHoliday()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
