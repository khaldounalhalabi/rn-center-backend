<?php

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->float('appointment_cost', 15, 4)->default(0.0000);
            $table->date('working_start_year')->default(now());
            $table->integer('max_appointments');
            $table->integer('appointment_day_range')->default(7);
            $table->integer("approximate_appointment_time")->default(30);
            $table->text('about_us')->nullable();
            $table->text('experience')->nullable();
            $table->enum("status", \App\Enums\ClinicStatusEnum::getAllValues())->default(\App\Enums\ClinicStatusEnum::ACTIVE->value);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Hospital::class)->nullable()->constrained();
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
