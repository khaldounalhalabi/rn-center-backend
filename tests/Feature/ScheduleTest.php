<?php

namespace Tests\Feature ;

use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Tests\Contracts\MainTestCase;

class ScheduleTest extends MainTestCase
{
    protected $model = Schedule::class;

    protected $resource = ScheduleResource::class;

    // define the actor
    protected $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.schedules.';

    // if your endpoints return the model with its relation put the relations in the array
    protected $relations = [];

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
        $this->requestPathHook($this->baseUrl . 'store');
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
