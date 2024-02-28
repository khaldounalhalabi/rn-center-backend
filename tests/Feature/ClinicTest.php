<?php

namespace Tests\Feature ;

use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use Tests\Contracts\MainTestCase;

class ClinicTest extends MainTestCase
{
    protected $model = Clinic::class;

    protected $resource = ClinicResource::class;

    // define the actor
    protected $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.clinics.';

    // if your endpoints return the model with its relation put the relations in the array
    protected $relations = ['user'];

    public function test_user_can_index_Clinic()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Clinic()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Clinic()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['work_gallery' => [\Illuminate\Http\UploadedFile::fake()->image('image.jpg')],
        ]);
    }

    public function test_user_can_update_Clinic()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['work_gallery' => [\Illuminate\Http\UploadedFile::fake()->image('image.jpg')],
        ]);
    }

    public function test_user_can_delete_a_Clinic()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
