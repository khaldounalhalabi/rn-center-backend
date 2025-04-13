<?php

use App\Models\Clinic;
use App\Models\ServiceCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('approximate_duration')->default(15);
            $table->unsignedDouble('price', 15, 4)->default(0.0000);
            $table->string('description')->nullable();
            $table->foreignIdFor(ServiceCategory::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
