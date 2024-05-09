<?php

namespace Tests\Feature;

use App\Http\Resources\AppointmentLogResource;
use App\Models\AppointmentLog;
use Tests\Contracts\MainTestCase;

class AppointmentLogTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = AppointmentLog::class;

    /** @var class-string */
    protected string $resource = AppointmentLogResource::class;

    // define the actor
    protected string $userType = "{{actor}}";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.public.appointment.logs.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_AppointmentLog()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_AppointmentLog()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_AppointmentLog()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['happen_in' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_update_AppointmentLog()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['happen_in' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_delete_a_AppointmentLog()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
