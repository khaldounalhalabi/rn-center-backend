<?php

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
        Schema::create('user_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->default('Unknown');
            $table->string('browser_type')->default('Unknown');
            $table->string('device_type')->default('Unknown');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_platforms');
    }
};
