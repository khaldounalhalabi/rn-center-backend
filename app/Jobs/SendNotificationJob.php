<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $notification;
    public $data;

    public function __construct($users, $notification, $data)
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
        try {
            Log::info("");
            Notification::send($this->users, new $this->notification($this->data));
            Log::info('***************************************');
            Log::info('***************************************');
        } catch (\Exception $exception) {
            Log::info('***************************************');
            Log::info("$this->notification could not be send to : \n" . print_r($this->users, 1));
            Log::info($exception->getMessage());
            Log::info('***************************************');
        }
    }
}
