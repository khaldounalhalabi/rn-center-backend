<?php

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
        Schema::create('payslip_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedDouble('amount', 15, 4)->default(0.0000);
            $table->text('reason')->nullable();
            $table->string('type');
            $table->foreignIdFor(\App\Models\Payslip::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip_adjustments');
    }
};
