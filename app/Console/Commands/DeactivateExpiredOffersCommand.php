<?php

namespace App\Console\Commands;

use App\Models\Offer;
use Illuminate\Console\Command;

class DeactivateExpiredOffersCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:deactivate-expired-offers-command';

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
        $this->info("Command Started");
        Offer::where('end_at', '<', now()->format('Y-m-d'))
            ->update([
                'is_active' => false
            ]);

        $this->info("Command Ended");
    }
}
