<?php

namespace Tests\Feature;

use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Tests\Contracts\MainTestCase;

class ServiceCategoryTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = ServiceCategory::class;

    /** @var class-string */
    protected string $resource = ServiceCategoryResource::class;

    // define the actor
    protected string $userType = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.service.categories.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_ServiceCategory()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_ServiceCategory()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_ServiceCategory()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_ServiceCategory()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_ServiceCategory()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
