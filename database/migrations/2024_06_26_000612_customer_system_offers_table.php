<?php

use App\Models\Customer;
use App\Models\SystemOffer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_system_offers', function (Blueprint $table) {
            $table->foreignIdFor(Customer::class);
            $table->foreignIdFor(SystemOffer::class);

            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_system_offers');
    }
};
