<?php

namespace Tests\Feature ;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Tests\Contracts\MainTestCase;

class TransactionTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Transaction::class;

    /** @var class-string */
    protected string $resource = TransactionResource::class;

    // define the actor
    protected string $userType = "{{actor}}";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.public.transactions.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Transaction()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Transaction()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Transaction()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['date' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_update_Transaction()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['date' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_delete_a_Transaction()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
