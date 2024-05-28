<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offer;

class DeactivateExpiredOffersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-expired-offers-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Offer::where('end_at' , '<' , now()->format('Y-m-d'))
            ->update([
                'is_active' => false
            ]);
    }
}
