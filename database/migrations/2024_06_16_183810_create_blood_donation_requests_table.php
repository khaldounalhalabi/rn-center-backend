<?php

use App\Models\City;
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
        Schema::create('blood_donation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('contact_phone');
            $table->string('address');
            $table->string('blood_group');
            $table->text('nearest_hospital');
            $table->text('notes')->nullable();
            $table->dateTime("can_wait_until")->default(now()->addDay());
            $table->foreignIdFor(City::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donation_requests');
    }
};
