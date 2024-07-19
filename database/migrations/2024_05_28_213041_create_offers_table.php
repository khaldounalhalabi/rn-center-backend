<?php

use App\Enums\OfferTypeEnum;
use App\Models\Clinic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->float('value');
            $table->json('note')->nullable();
            $table->date('start_at');
            $table->date('end_at');
            $table->boolean('is_active')->default(true);
            $table->string('type')->default(OfferTypeEnum::PERCENTAGE->value);
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['start_at', 'end_at']);
            $table->index(['start_at']);
            $table->index(['end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
