<?php

use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('medicine_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Prescription::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Medicine::class)->constrained()->cascadeOnDelete();
            $table->string('dosage')->nullable();
            $table->string('duration')->nullable();
            $table->string('time')->nullable();
            $table->string('dose_interval')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicine_prescriptions');
    }
};
