<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Keep the JSON column as-is
            $table->string('en')->virtualAs('JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))')->unique();
            $table->string('ar')->virtualAs('JSON_UNQUOTE(JSON_EXTRACT(name, "$.ar"))')->unique();
            $table->text('description')->nullable();
            $table->text('tags')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};
