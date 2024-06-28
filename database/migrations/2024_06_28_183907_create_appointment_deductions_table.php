<?php

use App\Enums\AppointmentDeductionStatusEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_deductions', function (Blueprint $table) {
            $table->id();
            $table->double('amount', 15, 4)->default(0.0000);
            $table->string('status')->default(AppointmentDeductionStatusEnum::PENDING->value);
            $table->date('date')->default(now());
            $table->foreignIdFor(ClinicTransaction::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Appointment::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_deductions');
    }
};
