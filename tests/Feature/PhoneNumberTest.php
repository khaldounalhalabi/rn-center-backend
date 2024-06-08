<?php

namespace Tests\Feature;

use App\Http\Resources\PhoneNumberResource;
use App\Models\PhoneNumber;
use Tests\Contracts\MainTestCase;

class PhoneNumberTest extends MainTestCase
{
    protected string $model = PhoneNumber::class;

    protected string $resource = PhoneNumberResource::class;

    // define the actor
    protected string $userType  = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.phone.numbers.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_PhoneNumber()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_PhoneNumber()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_PhoneNumber()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_PhoneNumber()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_PhoneNumber()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
