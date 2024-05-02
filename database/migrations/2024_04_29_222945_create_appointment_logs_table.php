<?php

use App\Models\Appointment;
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
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId("affected_id")->references('id')->on('users')->cascadeOnDelete();

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
