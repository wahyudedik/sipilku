<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\FactoryAnalyticsService;
use App\Models\Factory;
use App\Models\FactoryView;
use App\Models\FactoryReview;
use App\Models\User;
use Carbon\Carbon;

class FactoryAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_dashboard_summary_returns_expected_structure()
    {
        $user = User::factory()->create();
        $factory = Factory::create([
            'user_id' => $user->id,
            'name' => 'Test Factory',
            'slug' => 'test-factory',
            'status' => 'approved',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // create views
        FactoryView::create([
            'factory_id' => $factory->uuid,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'viewed_at' => Carbon::now(),
        ]);

        FactoryReview::create([
            'factory_id' => $factory->uuid,
            'user_id' => $user->id,
            'rating' => 5,
            'is_approved' => true,
            'created_at' => Carbon::now(),
        ]);

        $service = app(FactoryAnalyticsService::class);
        $result = $service->getDashboardSummary($factory->uuid, 7);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('views', $result);
        $this->assertArrayHasKey('orders_count', $result);
        $this->assertArrayHasKey('revenue', $result);
        $this->assertArrayHasKey('avg_rating', $result);
    }
}
