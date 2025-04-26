<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->cascadeOnDelete();
            $table->json('other_data')->nullable();
            $table->dateTime('next_visit')->nullable();
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
