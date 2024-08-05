<?php

use App\Enums\AppointmentDeductionStatusEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicTransaction;
use App\Models\Transaction;
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
            $table->dateTime('date')->default(now());
            $table->foreignIdFor(ClinicTransaction::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Clinic::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Transaction::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['date']);
            $table->index(['amount']);
            $table->index(['status']);
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
