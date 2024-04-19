<?php

namespace Tests\Feature ;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Tests\Contracts\MainTestCase;

class ServiceTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Service::class;

    /** @var class-string */
    protected string $resource = ServiceResource::class;

    // define the actor
    protected string $userType = "{{actor}}";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.services.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Service()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Service()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Service()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_Service()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_Service()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
