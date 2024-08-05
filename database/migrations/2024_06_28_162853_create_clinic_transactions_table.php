<?php

use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinic_transactions', function (Blueprint $table) {
            $table->id();
            $table->double('amount', 15, 4)->default(0.0000);
            $table->string('type')->default(ClinicTransactionTypeEnum::OUTCOME->value);
            $table->text('notes')->nullable();
            $table->string('status')->default(ClinicTransactionStatusEnum::PENDING->value);
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();
            $table->dateTime('date')->default(now());
            $table->double('before_balance', 15, 4)->default(0.0000);
            $table->double('after_balance', 15, 4)->default(0.0000);

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['date']);
            $table->index(['amount']);
            $table->index(['type']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_transactions');
    }
};
