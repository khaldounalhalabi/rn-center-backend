<?php

namespace Tests\Feature;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Tests\Contracts\MainTestCase;

class SettingTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Setting::class;

    /** @var class-string */
    protected string $resource = SettingResource::class;

    // define the actor
    protected string $userType = 'admin';

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.settings.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Setting()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Setting()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Setting()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest([]);
    }

    public function test_user_can_update_Setting()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([]);
    }

    public function test_user_can_delete_a_Setting()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
