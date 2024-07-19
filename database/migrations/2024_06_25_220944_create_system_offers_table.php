<?php

use App\Enums\OfferTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default(OfferTypeEnum::FIXED->value);
            $table->unsignedDouble('amount', 15, 4)->default(0.0000);
            $table->unsignedBigInteger('allowed_uses')->default(0);
            $table->boolean('allow_reuse')->default(false);
            $table->date('from')->default(now());
            $table->date('to')->default(now()->addDay());

            $table->timestamps();
            $table->index(['created_at']);
            $table->index(['from', 'to']);
            $table->index(['from']);
            $table->index(['to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_offers');
    }
};
