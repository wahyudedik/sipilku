<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AggregateFactoryAnalytics extends Command
{
    protected $signature = 'analytics:aggregate-factories {date?}';
    protected $description = 'Aggregate factory analytics daily';

    public function handle()
    {
        $date = Carbon::parse($this->argument('date') ?? now()->toDateString())->toDateString();
        $this->info("Aggregating factory analytics for {$date}...");

        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();

        $factories = DB::table('factories')->select('uuid')->get();
        foreach ($factories as $factory) {
            $views = DB::table('factory_views')->where('factory_id', $factory->uuid)->whereBetween('viewed_at', [$from, $to])->count();
            $sales = DB::table('orders')->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.factory_id', $factory->uuid)
                ->whereBetween('orders.created_at', [$from, $to])
                ->selectRaw('sum(order_items.quantity) as orders_count, sum(order_items.price * order_items.quantity) as revenue')
                ->first();
            $avgRating = DB::table('factory_reviews')->where('factory_id', $factory->uuid)
                ->whereBetween('created_at', [$from, $to])->avg('rating');

            $productPopularity = DB::table('order_items')
                ->join('factory_products', 'factory_products.id', '=', 'order_items.factory_product_id')
                ->where('factory_products.factory_id', $factory->uuid)
                ->whereBetween('order_items.created_at', [$from, $to])
                ->select('factory_products.id as product_id', 'factory_products.name as name', DB::raw('sum(order_items.quantity) as cnt'), DB::raw('sum(order_items.quantity * order_items.price) as revenue'))
                ->groupBy('factory_products.id', 'factory_products.name')
                ->orderByDesc('cnt')
                ->limit(10)
                ->get();

            DB::table('factory_analytics_aggregates')->updateOrInsert(
                ['factory_id' => $factory->uuid, 'date' => $date],
                [
                    'views' => $views,
                    'orders_count' => (int)($sales->orders_count ?? 0),
                    'revenue' => (float)($sales->revenue ?? 0),
                    'avg_rating' => $avgRating ? round($avgRating, 2) : null,
                    'product_popularity' => $productPopularity->isNotEmpty() ? $productPopularity->toJson() : null,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Done.');
    }
}
