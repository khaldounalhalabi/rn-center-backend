<?php

namespace App\Modules\Notification\App\Jobs;

use Error;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection|array|mixed
     */
    public mixed $users;
    public string $notification;
    public array $data;

    /**
     * @param Collection|array|mixed $users
     * @param string                 $notification
     * @param array                  $data
     */
    public function __construct(mixed $users, string $notification, array $data)
    {
        $this->users = $users;
        $this->notification = $notification;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(app()->environment());
        if (app()->environment('local')) {
            Log::info('***************************************');
            Log::info('***************************************');
            Log::info("Send Notification Job : [$this->notification]");
            Notification::send($this->users, new $this->notification($this->data));
            Log::info('***************************************');
            Log::info('***************************************');
        } else {
            try {
                Log::info('***************************************');
                Log::info('***************************************');
                Log::info("Send Notification Job : [$this->notification]");
                Notification::send($this->users, new $this->notification($this->data));
                Log::info('***************************************');
                Log::info('***************************************');

            } catch (Exception|Error|Throwable $exception) {
                Log::info('***************************************');
                Log::info("Error While Sending Notification [$this->notification]");
                Log::info("$this->notification could not be send to : \n" . print_r($this->users->pluck('id')->toArray(), 1));
                Log::info($exception->getMessage());
                Log::info('***************************************');
            }
        }

    }
}
