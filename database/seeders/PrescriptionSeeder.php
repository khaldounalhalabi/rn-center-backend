<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatusEnum;
use App\Enums\MedicinePrescriptionStatusEnum;
use App\Models\Appointment;
use App\Models\Medicine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = Medicine::all();
        $appointments = Appointment::where('status', AppointmentStatusEnum::CHECKOUT->value)->get();

        $prescriptions = [];

        foreach ($appointments as $appointment) {

            $other_data = [
                ['key' => 'الوزن', 'value' => rand(50, 90) . ' kg'],
                ['key' => 'ضغط الدم', 'value' => rand(110, 140) . '/' . rand(70, 90) . ' mmHg'],
                ['key' => 'الحرارة', 'value' => rand(36, 38) . ' °C'],
            ];

            $prescriptions[] = [
                'clinic_id' => $appointment->clinic_id,
                'customer_id' => $appointment->customer_id,
                'appointment_id' => $appointment->id,
                'other_data' => json_encode($other_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'next_visit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('prescriptions')->insert($prescriptions);

        $prescriptionIds = DB::table('prescriptions')->pluck('id');

        $medicinePrescriptions = [];

        foreach ($prescriptionIds as $prescriptionId) {
            $numMedicines = rand(1, 3);
            $selectedMedicines = $medicines->random($numMedicines);

            foreach ($selectedMedicines as $medicine) {
                $dosage = rand(1, 2) . ' ' . $medicine->quantity_unit ?? 'قطعة';
                $doseInterval = rand(6, 12) . ' ساعة';
                $medicinePrescriptions[] = [
                    'prescription_id' => $prescriptionId,
                    'medicine_id' => $medicine->id,
                    'dosage' => $dosage,
                    'dose_interval' => $doseInterval,
                    'comment' => 'استخدم بعد الطعام',
                    'status' => fake()->randomElement(MedicinePrescriptionStatusEnum::getAllValues()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('medicine_prescriptions')->insert($medicinePrescriptions);
    }
}
