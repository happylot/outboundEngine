<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class OutboundSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_brain_page_is_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outbound/sales-brain');
        $response->assertStatus(200);
    }

    public function test_outbound_leads_page_is_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outbound/leads');
        $response->assertStatus(200);
    }

    public function test_research_workspace_page_is_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outbound/research');
        $response->assertStatus(200);
    }

    public function test_reactivation_engine_page_is_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/outbound/reactivation');
        $response->assertStatus(200);
    }
}
