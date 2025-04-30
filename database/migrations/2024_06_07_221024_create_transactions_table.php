<?php

use App\Models\Appointment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->double('amount', 15, 4)->default(0.0000);
            $table->text('description')->nullable();
            $table->dateTime('date')->default(now());
            $table->foreignId('actor_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignIdFor(Appointment::class)->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['date']);
            $table->index(['amount']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
