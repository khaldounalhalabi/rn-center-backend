<?php

use App\Enums\PayrunStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payruns', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(PayrunStatusEnum::DRAFT->value);
            $table->date('should_delivered_at');
            $table->string('payment_date');
            $table->double('payment_cost', 15, 4)->default(0.0000);
            $table->string('period');
            $table->date('from');
            $table->date('to');
            $table->boolean('has_errors')->default(false);
            $table->dateTime('processed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payruns');
    }
};
