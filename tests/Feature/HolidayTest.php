<?php

namespace Tests\Feature;

use App\Http\Resources\v1\HolidayResource;
use App\Models\Holiday;
use Tests\Contracts\MainTestCase;

class HolidayTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Holiday::class;

    /** @var class-string */
    protected string $resource = HolidayResource::class;

    // define the actor
    protected string $userType = 'admin';

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.holidays.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Holiday()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Holiday()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Holiday()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['from' => now()->format('Y-m-d H:i:s'),
            'to' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_update_Holiday()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['from' => now()->format('Y-m-d H:i:s'),
            'to' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_delete_a_Holiday()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
