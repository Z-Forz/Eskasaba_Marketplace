<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AutoCompleteOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah status pesanan yang telah 3 hari dibuat menjadi Selesai (Completed)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Order::autoCompleteExpiredOrders();
        $this->info("Berhasil mengonfirmasi {$count} pesanan yang telah 3 hari dibuat menjadi Selesai.");
        return Command::SUCCESS;
    }
}
