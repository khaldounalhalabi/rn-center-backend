<?php

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->json('full_name')->nullable();
            $table->json('first_name');
            $table->json('middle_name');
            $table->json('last_name');
            $table->string('email')->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->enum('gender', GenderEnum::getAllValues());
            $table->enum('blood_group', BloodGroupEnum::getAllValues())->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('reset_password_code')->nullable();
            $table->dateTime('reset_code_valid_until')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['full_name']);
            $table->index(['first_name']);
            $table->index(['middle_name']);
            $table->index(['last_name']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
