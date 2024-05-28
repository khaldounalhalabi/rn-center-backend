<?php

namespace Tests\Feature ;

use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Tests\Contracts\MainTestCase;

class OfferTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Offer::class;

    /** @var class-string */
    protected string $resource = OfferResource::class;

    // define the actor
    protected string $userType = "{{actor}}";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.offers.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Offer()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Offer()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Offer()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_Offer()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_Offer()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
