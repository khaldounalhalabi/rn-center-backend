<?php

use App\Models\Appointment;
use App\Models\SystemOffer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_system_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SystemOffer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Appointment::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_system_offers');
    }
};
