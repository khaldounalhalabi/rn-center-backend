<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
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
    protected $relations = ['media'];

    public function setUp(): void
    {
        parent::setUp();
        $this->user->delete();
    }

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
        $this->storeTest([
            'image' => UploadedFile::fake()->image('image.jpg'),
            'phone_number' => ["00964728925489"],
            'password_confirmation' => '123456789',
            'birth_date' => Carbon::now()->format('Y-m-d'),
        ], [], true);
    }

    public function test_user_can_update_User()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest([
            'image' => UploadedFile::fake()->image('image.jpg'),
            'phone_number' => '12345678910',
            'password_confirmation' => '123456789',
            'birth_date' => Carbon::now()->format('Y-m-d'),
        ], [], false, false);
    }

    public function test_user_can_delete_a_User()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
