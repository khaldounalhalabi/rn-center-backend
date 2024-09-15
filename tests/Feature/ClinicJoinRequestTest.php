<?php

namespace Tests\Feature;

use App\Http\Resources\v1\ClinicJoinRequestResource;
use App\Models\ClinicJoinRequest;
use Tests\Contracts\MainTestCase;

class ClinicJoinRequestTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = ClinicJoinRequest::class;

    /** @var class-string */
    protected string $resource = ClinicJoinRequestResource::class;

    // define the actor
    protected string $userType = 'admin';

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.clinic.join.requests.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_ClinicJoinRequest()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_ClinicJoinRequest()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_ClinicJoinRequest()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_ClinicJoinRequest()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_ClinicJoinRequest()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
