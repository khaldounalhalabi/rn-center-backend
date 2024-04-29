<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_logs', function (Blueprint $table) {
            $table->id();
            $table->text('cancellation_reason')->unique();
            $table->string('status')->nullable()->unique();
            $table->dateTime('happen_in')->nullable()->unique();
            $table->foreignIdFor(\App\Models\Appointment::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Actor::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Affected::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_logs');
    }
};
