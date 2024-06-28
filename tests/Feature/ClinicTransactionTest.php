<?php

namespace Tests\Feature;

use App\Http\Resources\ClinicTransactionResource;
use App\Models\ClinicTransaction;
use Tests\Contracts\MainTestCase;

class ClinicTransactionTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = ClinicTransaction::class;

    /** @var class-string */
    protected string $resource = ClinicTransactionResource::class;

    // define the actor
    protected string $userType = 'doctor';

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.doctor.clinic.transactions.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_ClinicTransaction()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_ClinicTransaction()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_ClinicTransaction()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_ClinicTransaction()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_ClinicTransaction()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
