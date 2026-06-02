<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HealthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_health_endpoint_returns_ok(): void {
        $response = $this->get('/health');
        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
    }
}
