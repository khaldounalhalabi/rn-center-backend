<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'label' => 'zain_cash_number',
            'value' => '654987531685498798',
        ]);
        Setting::create([
            'label' => 'contact_number_1',
            'value' => '07123456789',
        ]);
        Setting::create([
            'label' => 'contact_number_2',
            'value' => '07987654321',
        ]);
        Setting::create([
            'label' => 'days_before_notify_for_expiration',
            'value' => 5,
        ]);

        Setting::create([
            'label' => "terms_of_service",
            'value' => 'some text',
        ]);

        Setting::create([
            'label' => 'clinic_contract',
            'value' => 'The clinics contract'
        ]);

        Setting::create([
            'label' => 'zain_cash_qr',
            'value' => null
        ]);
    }
}
