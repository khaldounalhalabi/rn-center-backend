<?php

namespace Tests\Feature;

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\UploadedFile;
use Tests\Contracts\MainTestCase;

class AppointmentTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Appointment::class;

    /** @var class-string */
    protected string $resource = AppointmentResource::class;

    // define the actor
    protected string $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.appointments.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Appointment()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Appointment()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Appointment()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['qr_code' => UploadedFile::fake()->image('image.jpg'),
        ]);
    }

    public function test_user_can_update_Appointment()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['qr_code' => UploadedFile::fake()->image('image.jpg'),
        ]);
    }

    public function test_user_can_delete_a_Appointment()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
