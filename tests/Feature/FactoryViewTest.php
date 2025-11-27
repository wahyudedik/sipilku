<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Factory;
use App\Models\FactoryView;

class FactoryViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_show_records_view()
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

        $response = $this->get(route('factories.show', $factory));
        $response->assertStatus(200);

        $this->assertDatabaseHas('factory_views', [
            'factory_id' => $factory->uuid,
        ]);
    }
}
