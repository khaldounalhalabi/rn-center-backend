<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $to;
    public string $message;
    public int $customerId;
    public bool $urgent = false;

    /**
     * Create a new job instance.
     */
    public function __construct(string $to, string $message, int $customerId, bool $urgent = false)
    {
        $this->to = $to;
        $this->message = $message;
        $this->customerId = $customerId;
        $this->urgent = $urgent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("############# sending sms. ############# ");
            SmsService::make()->sendMessage($this->to, $this->message, $this->customerId, $this->urgent);
            Log::info("#############  ############# ");
        } catch (\Exception|\Error $e) {
            Log::info("############# Error sending sms. ############# ");
            Log::info("{$e->getMessage()} \n {$e->getFile()} \n Line: {$e->getLine()}");
            Log::info("########################## ");
        }
    }
}
