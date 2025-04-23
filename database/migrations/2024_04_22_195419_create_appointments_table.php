<?php

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Service::class)->nullable()->constrained()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->unsignedDouble('extra_fees', 15, 4)->default(0.0000);
            $table->double('total_cost', 15, 4)->default(0.0000);
            $table->string('type')->default(AppointmentTypeEnum::MANUAL->value);
            $table->dateTime('date_time');
            $table->string('status')->default(AppointmentStatusEnum::PENDING->value);
            $table->bigInteger('appointment_sequence')->nullable();
            $table->unsignedDouble('discount', 15, 4)->default(0.0000);
            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['date_time']);
            $table->index(['type']);
            $table->index(['appointment_sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
