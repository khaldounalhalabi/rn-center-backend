<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->governorates as $governorate) {
            City::updateOrCreate([
                'name' => json_encode($governorate, JSON_UNESCAPED_SLASHES)
            ]);
        }
    }

    public array $governorates = [
        ["en" => "Baghdad", "ar" => "بغداد"],
        ["en" => "Basra", "ar" => "البصرة"],
        ["en" => "Mosul", "ar" => "الموصل"],
        ["en" => "Erbil", "ar" => "أربيل"],
        ["en" => "Karbala", "ar" => "كربلاء"],
        ["en" => "Najaf", "ar" => "النجف"],
        ["en" => "Kirkuk", "ar" => "كركوك"],
        ["en" => "Dohuk", "ar" => "دهوك"],
        ["en" => "Maysan", "ar" => "ميسان"],
        ["en" => "Muthanna", "ar" => "المثنى"],
        ["en" => "Wasit", "ar" => "واسط"],
        ["en" => "Diyala", "ar" => "ديالى"],
        ["en" => "Saladin", "ar" => "صلاح الدين"],
        ["en" => "Al-Qadisiyyah", "ar" => "القادسية"],
        ["en" => "Anbar", "ar" => "الأنبار"],
        ["en" => "Kerbala", "ar" => "كربلاء"],
        ["en" => "Sulaymaniyah", "ar" => "السليمانية"],
        ["en" => "Nineveh", "ar" => "نينوى"],
        ["en" => "Dhi Qar", "ar" => "ذي قار"],
        ["en" => "Babil", "ar" => "بابل"],
        ["en" => "Thi-Qar", "ar" => "ذي قار"],
        ["en" => "Najaf", "ar" => "النجف"],
        ["en" => "Kirkuk", "ar" => "كركوك"],
        ["en" => "Al-Sulaymaniyah", "ar" => "السليمانية"],
        ["en" => "Al-Muthanna", "ar" => "المثنى"],
        ["en" => "Al-Diwaniyah", "ar" => "الديوانية"],
        ["en" => "Halabja", "ar" => "حلبجة"]
    ];
}
