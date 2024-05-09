<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public array $governorates = [
        ["en" => "Najaf", "ar" => "النجف"],
        ["en" => "Karbala", "ar" => "كربلاء"],
        ["en" => "Basra", "ar" => "البصرة"],
        ["en" => "Muthanna", "ar" => "المثنى"],
        ["en" => "Qadisiyah", "ar" => "القادسية"],
        ["en" => "Dhi-Qar", "ar" => "ذي قار"],
        ["en" => "Maysan", "ar" => "ميسان"],
        ["en" => "Wasit", "ar" => "واسط"],
        ["en" => "Salah al-Din", "ar" => "صلاح الدين"],
        ["en" => "Sulaymaniyah", "ar" => "السليمانية"],
        ["en" => "Diyala", "ar" => "ديالى"],
        ["en" => "Babil", "ar" => "بابل"],
        ["en" => "Baghdad", "ar" => "بغداد"],
        ["en" => "Anbar", "ar" => "الأنبار"],
        ["en" => "Arbil", "ar" => "أربيل"],
        ["en" => "Kirkuk", "ar" => "كركوك"],
        ["en" => "Duhok", "ar" => "دهوك"],
        ["en" => "Mosul", "ar" => "الموصل"]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->governorates as $governorate) {
            City::updateOrCreate([
                'name' => json_encode($governorate, JSON_UNESCAPED_UNICODE)
            ]);
        }
    }
}
