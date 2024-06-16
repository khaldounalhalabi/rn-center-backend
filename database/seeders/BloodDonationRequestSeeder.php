<?php

namespace Database\Seeders;

use App\Models\BloodDonationRequest;
use Illuminate\Database\Seeder;

class BloodDonationRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BloodDonationRequest::factory(10)->create();
    }
}
