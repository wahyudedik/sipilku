<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Factory;
use App\Models\FactoryView;

class FactoryAnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_metrics_for_owner()
    {
        $user = User::factory()->create();
        $factory = Factory::create([
            'user_id' => $user->id,
            'name' => 'Analytics Factory',
            'slug' => 'analytics-factory',
            'status' => 'approved',
            'is_verified' => true,
            'is_active' => true,
        ]);

        FactoryView::create([
            'factory_id' => $factory->uuid,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'viewed_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('factories.analytics.dashboard', $factory));
        $response->assertStatus(200);
        $response->assertJsonStructure(['views', 'orders_count', 'revenue', 'avg_rating']);
    }
}
