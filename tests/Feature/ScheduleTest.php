<?php

namespace Tests\Feature;

use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Tests\Contracts\MainTestCase;

class ScheduleTest extends MainTestCase
{
    protected string $model = Schedule::class;

    protected string $resource = ScheduleResource::class;

    // define the actor
    protected string $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.clinics.schedules.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Schedule()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Schedule()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Schedule()
    {
        $this->requestPathHook(route('api.admin.clinics.schedules'));
        $this->storeTest([]);
    }

    public function test_user_can_update_Schedule()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_Schedule()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
