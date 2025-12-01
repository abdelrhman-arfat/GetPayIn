<?php

namespace App\Console\Commands;

use App\Domain\Interfaces\RedisInterface;
use App\Models\Hold;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanHolds extends Command
{

    protected $signature = 'holds:cleanup';
    protected $description = 'Delete expired holds every 2 minutes';
    private RedisInterface $redis;

    public function __construct(RedisInterface $redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expiredHolds = Hold::expired()
            ->get(['id', 'product_id', 'quantity']);

        if ($expiredHolds->isEmpty()) {
            $this->info('No expired holds to clean.');
            return;
        }

        $expiredHolds->groupBy('product_id')->each(function ($holds, $productId) {
            $totalQty = $holds->sum('quantity');
            DB::table('products')->where('id', $productId)->increment('stock', $totalQty);
            $this->redis->delete("products:$productId");
        });

        Hold::whereIn('id', $expiredHolds->pluck('id'))->delete();

        $this->info('Expired holds cleared successfully!');
    }
}
