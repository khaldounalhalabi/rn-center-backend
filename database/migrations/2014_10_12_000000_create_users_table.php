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
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->date('birth_date')->nullable();
            $table->enum('gender', GenderEnum::getAllValues());
            $table->enum('blood_group', BloodGroupEnum::getAllValues())->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->text('tags')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('password');
            $table->string('fcm_token')->nullable();
            $table->string('reset_password_code')->nullable();
            $table->boolean('is_archived')->default(false);

            $table->rememberToken();
            $table->timestamps();
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
