<?php

namespace Tests\Feature;

use App\Http\Resources\EnquiryResource;
use App\Models\Enquiry;
use Tests\Contracts\MainTestCase;

class EnquiryTest extends MainTestCase
{
    /** @var class-string */
    protected string $model = Enquiry::class;

    /** @var class-string */
    protected string $resource = EnquiryResource::class;

    // define the actor
    protected string $userType  = "admin";

    // the named route eg: 'user.products.'
    //!!! Note: the dot "." in the end of the baseUrl is important !!!
    protected string $baseUrl = 'api.admin.enquiries.';

    // if your endpoints return the model with its relation put the relations in the array
    protected array $relations = [];

    public function test_user_can_index_Enquiry()
    {
        $this->requestPathHook($this->baseUrl . 'index');
        $this->indexTest();
    }

    public function test_user_can_show_a_Enquiry()
    {
        $this->requestPathHook($this->baseUrl . 'show');
        $this->showTest();
    }

    public function test_user_can_create_a_Enquiry()
    {
        $this->requestPathHook($this->baseUrl . 'store');
        $this->storeTest(['read_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_update_Enquiry()
    {
        $this->requestPathHook($this->baseUrl . 'update');
        $this->updateTest(['read_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_user_can_delete_a_Enquiry()
    {
        $this->requestPathHook($this->baseUrl . 'destroy');
        $this->deleteTest();
    }
}
