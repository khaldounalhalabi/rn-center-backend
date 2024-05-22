<?php

use App\Enums\SubscriptionStatusEnum;
use App\Models\Clinic;
use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subscription::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Clinic::class)->constrained()->cascadeOnDelete();
            $table->dateTime('start_time')->default(now());
            $table->dateTime('end_time')->default(now()->addYear());
            $table->string('status')->default(SubscriptionStatusEnum::ACTIVE->value);
            $table->unsignedFloat('deduction_cost')->default(0.00);
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_subscriptions');
    }
};
