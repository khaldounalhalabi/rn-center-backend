<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->json('name')->default('{"en":"" , "ar":""}');
            $table->foreignIdFor(City::class)->nullable()->constrained()->cascadeOnDelete();
            $table->text('lat')->nullable();
            $table->text('lng')->nullable();
            $table->text("map_iframe")->nullable();
            $table->string('country')->default('Iraq');
            $table->unsignedBigInteger('addressable_id');
            $table->string('addressable_type');
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
