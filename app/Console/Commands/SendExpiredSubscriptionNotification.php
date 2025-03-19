<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\Clinic\YourSubscriptionIsAboutToExpire;
use App\Services\FirebaseServices;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendExpiredSubscriptionNotification extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:send-expired-subscription-notification';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $before = intval(Setting::where('label', 'days_before_notify_for_expiration')->first()?->value ?? 7);
        FirebaseServices::make()
            ->setMethod(FirebaseServices::ToQuery)
            ->setTo(
                User::whereHas('clinic', function (Builder $builder) use ($before) {
                    $builder->withoutGlobalScope('available_online')
                        ->whereHas('activeSubscription', function (Builder $builder) use ($before) {
                            $builder->whereDate('end_time', '=', now()->addDays($before));
                        });
                })->orWhereHas('clinicEmployee', function (Builder $query) use ($before) {
                    $query
                        ->withoutGlobalScope('available_online')
                        ->whereHas('clinic', function (Builder $q) use ($before) {
                            $q->whereHas('activeSubscription', function (Builder $b) use ($before) {
                                $b->whereDate('end_time', '=', now()->addDays($before));
                            });
                        });
                })
            )->setData([
                'left_days' => $before,
            ])
            ->setNotification(YourSubscriptionIsAboutToExpire::class)
            ->send();
    }
}
