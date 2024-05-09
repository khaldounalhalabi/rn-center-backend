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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string("schedulable_type");
            $table->unsignedBigInteger("schedulable_id");
            $table->string('day_of_week');
            $table->time('start_time')->default(now()->format('H:i'));
            $table->time('end_time')->default(now()->format('H:i'));
            $table->integer('appointment_gap')->default(10);

            $table->unique([
                'schedulable_type',
                'schedulable_id',
                'day_of_week',
                'start_time',
                'end_time'
            ], 'unique_schedule');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
