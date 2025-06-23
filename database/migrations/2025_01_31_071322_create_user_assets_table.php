<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_assets', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->integer('checkin_condition')->nullable();
            $table->integer('checkout_condition')->nullable();
            $table->dateTime('checkin_date');
            $table->dateTime('checkout_date')->nullable();
            $table->foreignIdFor(Asset::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->unsignedDouble('quantity')->default(0);
            $table->date('expected_return_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assets');
    }
};
