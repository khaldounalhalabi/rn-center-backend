<?php

namespace Tests\Feature ;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\Contracts\MainTestCase;

class UserTest extends MainTestCase
{
    protected $model = User::class;

    protected $resource = UserResource::class;

    // define the actor
    protected $userType = "none";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.users.';

    // if your endpoints return the model with its relation put the relations in the array
    protected $relations = [];

    public function test_user_can_index_User()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_User()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_User()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['image' => \Illuminate\Http\UploadedFile::fake()->image('image.jpg'),
        ]);
    }

    public function test_user_can_update_User()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['image' => \Illuminate\Http\UploadedFile::fake()->image('image.jpg'),
        ]);
    }

    public function test_user_can_delete_a_User()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
