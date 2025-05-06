<?php

use App\Enums\PayslipStatusEnum;
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
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->integer('paid_days')->default(0);
            $table->double('gross_pay', 15, 4)->default(0.0000);
            $table->double('net_pay', 15, 4)->default(0.0000);
            $table->string('status')->default(PayslipStatusEnum::DRAFT->value);
            $table->json('error')->nullable();
            $table->text('details')->nullable();
            $table->boolean('edited_manually')->default(false);
            $table->foreignIdFor(App\Models\Payrun::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\Formula::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
