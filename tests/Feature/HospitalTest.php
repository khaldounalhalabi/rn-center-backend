<?php

namespace Tests\Feature;

use App\Http\Resources\HospitalResource;
use App\Models\Hospital;
use Illuminate\Http\UploadedFile;
use Tests\Contracts\MainTestCase;

class HospitalTest extends MainTestCase
{
    protected string $model = Hospital::class;

    protected string $resource = HospitalResource::class;

    // define the actor
    protected string $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.hospitals.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Hospital()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Hospital()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Hospital()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['images' => [UploadedFile::fake()->image('image.jpg')],], []);
    }

    public function test_user_can_update_Hospital()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['images' => [UploadedFile::fake()->image('image.jpg')],]);
    }

    public function test_user_can_delete_a_Hospital()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
