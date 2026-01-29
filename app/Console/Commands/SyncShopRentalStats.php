<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ShopRentalSyncService;

class SyncShopRentalStats extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sync:shop-rental';

    /**
     * The console command description.
     */
    protected $description = 'Sync shop rental stats from ChargeKingdom API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⏳ Syncing shop rental stats...');

        app(ShopRentalSyncService::class)->sync();

        $this->info('✅ Done syncing shop rental stats');

        return Command::SUCCESS;
    }
}
