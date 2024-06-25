<?php

namespace Tests\Feature;

use App\Http\Resources\SystemOfferResource;
use App\Models\SystemOffer;
use Tests\Contracts\MainTestCase;

class SystemOfferTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = SystemOffer::class;

    /** @var class-string */
    protected string $resource = SystemOfferResource::class;

    // define the actor
    protected string $userType = 'admin';

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.system.offers.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_SystemOffer()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_SystemOffer()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_SystemOffer()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_SystemOffer()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_SystemOffer()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
