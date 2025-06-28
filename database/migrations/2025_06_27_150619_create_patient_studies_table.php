<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_studies', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('patient_uuid');
            $table->string('study_uuid');
            $table->string('study_uid');
            $table->dateTime('study_date');
            $table->text('title');
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->json('available_modes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_studies');
    }
};
