<?php

use App\Models\App\Models\Speciality;
use App\Models\App\Models\Clinic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_specialities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Speciality::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Clinic::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_specialities');
    }
};
