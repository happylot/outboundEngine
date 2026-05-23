<?php

namespace Tests\Feature;

use App\Models\SeederAccount;
use App\Models\SeedingBehaviorGroup;
use App\Models\SeedingDevice;
use App\Models\SeedingIncident;
use App\Models\SeedingLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Seeding\TesterWorkspace;


class SeedingRndTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private SeedingDevice $device;
    private SeedingBehaviorGroup $behaviorGroup;
    private SeederAccount $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->device = SeedingDevice::create([
            'device_name' => 'iPhone 11 Pro',
            'device_model' => 'A2111',
            'os_version' => 'iOS 15.4',
            'connection_type' => 'Wi-Fi',
        ]);

        $this->behaviorGroup = SeedingBehaviorGroup::create([
            'name' => 'Nhóm Test',
            'daily_limit' => 10,
            'min_interval_minutes' => 15,
            'max_interval_minutes' => 30,
        ]);

        $this->account = SeederAccount::create([
            'username' => 'test_seeder',
            'platform' => 'facebook',
            'age_group' => '1-3 years',
            'device_id' => $this->device->id,
            'behavior_group_id' => $this->behaviorGroup->id,
            'status' => 'active',
        ]);
    }

    public function test_workspace_requires_authentication(): void
    {
        $this->get(route('seeding.workspace'))
            ->assertRedirect(route('login'));
    }

    public function test_workspace_renders_successfully_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('seeding.workspace'))
            ->assertStatus(200);
    }

    public function test_can_select_account_in_workspace(): void
    {
        Livewire::actingAs($this->user)
            ->test(TesterWorkspace::class)
            ->call('selectAccount', $this->account->id)
            ->assertSet('selectedAccountId', $this->account->id);
    }

    public function test_can_log_successful_seeding_action_with_cooldown(): void
    {
        Livewire::actingAs($this->user)
            ->test(TesterWorkspace::class)
            ->set('selectedAccountId', $this->account->id)
            ->set('action_type', 'comment')
            ->set('content_type', 'hand-written-short')
            ->set('content', 'Great post!')
            ->set('log_status', 'success')
            ->call('submitLog')
            ->assertHasNoErrors();

        // Assert log was created
        $this->assertDatabaseHas('seeding_logs', [
            'seeder_account_id' => $this->account->id,
            'action_type' => 'comment',
            'status' => 'success',
            'content' => 'Great post!',
        ]);

        // Refetch account and assert state updates
        $this->account->refresh();
        $this->assertEquals('cooldown', $this->account->status);
        $this->assertNotNull($this->account->cooldown_until);
        $this->assertNotNull($this->account->last_active_at);

        // Verify cooldown is between min_interval (15m) and max_interval (30m)
        $cooldownDiff = now()->diffInMinutes($this->account->cooldown_until);
        $this->assertTrue($cooldownDiff >= 14 && $cooldownDiff <= 31);
    }

    public function test_can_log_checkpoint_incident(): void
    {
        Livewire::actingAs($this->user)
            ->test(TesterWorkspace::class)
            ->set('selectedAccountId', $this->account->id)
            ->set('action_type', 'post')
            ->set('log_status', 'checkpoint_triggered')
            ->set('incident_description', 'Phone verification requested')
            ->call('submitLog')
            ->assertHasNoErrors();

        // Refetch account and assert checkpoint status
        $this->account->refresh();
        $this->assertEquals('checkpoint', $this->account->status);
        $this->assertNull($this->account->cooldown_until);

        // Assert database incident was created
        $this->assertDatabaseHas('seeding_incidents', [
            'seeder_account_id' => $this->account->id,
            'incident_type' => 'checkpoint',
            'description' => 'Phone verification requested',
            'resolved_at' => null,
        ]);
    }

    public function test_can_resolve_checkpoint(): void
    {
        // 1. Setup account in checkpoint state with active incident
        $this->account->update(['status' => 'checkpoint']);
        
        $log = SeedingLog::create([
            'seeder_account_id' => $this->account->id,
            'action_type' => 'post',
            'status' => 'checkpoint_triggered',
        ]);

        $incident = SeedingIncident::create([
            'seeder_account_id' => $this->account->id,
            'incident_type' => 'checkpoint',
            'trigger_action_id' => $log->id,
            'description' => 'Verification checkpoint',
        ]);

        // 2. Call resolveCheckpoint
        Livewire::actingAs($this->user)
            ->test(TesterWorkspace::class)
            ->set('selectedAccountId', $this->account->id)
            ->set('checkpoint_resolve_notes', 'Verified with SMS Code')
            ->call('resolveCheckpoint')
            ->assertHasNoErrors();

        // 3. Assert account status is set back to active
        $this->account->refresh();
        $this->assertEquals('active', $this->account->status);
        $this->assertNull($this->account->cooldown_until);

        // 4. Assert incident is resolved
        $incident->refresh();
        $this->assertNotNull($incident->resolved_at);
        $this->assertNotNull($incident->recovery_time_minutes);
        $this->assertStringContainsString('Verified with SMS Code', $incident->description);
    }

    public function test_scenarios_requires_authentication(): void
    {
        $this->get(route('seeding.scenarios'))
            ->assertRedirect(route('login'));
    }

    public function test_scenarios_page_renders_successfully(): void
    {
        $this->actingAs($this->user)
            ->get(route('seeding.scenarios'))
            ->assertStatus(200);
    }

    public function test_can_create_scenario_and_automatically_generate_templates(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Seeding\SeedingScenarios::class)
            ->set('title', 'Sản phẩm quá đắt')
            ->set('brand_name', 'Mỹ phẩm ABC')
            ->set('target_link', 'https://facebook.com/post/123')
            ->set('context', 'Khách hàng liên tục phàn nàn giá bán của hãng quá đắt so với đối thủ')
            ->call('createScenario')
            ->assertHasNoErrors();

        // Assert Scenario was created
        $this->assertDatabaseHas('seeding_scenarios', [
            'title' => 'Sản phẩm quá đắt',
            'brand_name' => 'Mỹ phẩm ABC',
        ]);

        // Assert AI Templates were generated (2 templates per 4 angles = 8 templates)
        $this->assertDatabaseCount('seeding_templates', 8);
        $this->assertDatabaseHas('seeding_templates', [
            'angle' => 'tham',
        ]);
        $this->assertDatabaseHas('seeding_templates', [
            'angle' => 'san',
        ]);
    }

    public function test_leads_crm_requires_authentication(): void
    {
        $this->get(route('seeding.leads'))
            ->assertRedirect(route('login'));
    }

    public function test_leads_crm_page_renders_successfully(): void
    {
        $this->actingAs($this->user)
            ->get(route('seeding.leads'))
            ->assertStatus(200);
    }

    public function test_can_log_lead_from_workspace_and_sync_mock(): void
    {
        Livewire::actingAs($this->user)
            ->test(TesterWorkspace::class)
            ->set('selectedAccountId', $this->account->id)
            ->set('lead_name', 'Nguyễn Thị Khách')
            ->set('lead_uid', 'khach.nguyen.99')
            ->set('lead_phone', '0987654321')
            ->set('lead_email', 'khach@example.com')
            ->set('lead_profile_url', 'https://facebook.com/khach.nguyen.99')
            ->set('lead_comment_url', 'https://facebook.com/groups/seeding/posts/1')
            ->set('lead_content', 'Tư vấn cho mình với nha')
            ->call('submitLead')
            ->assertHasNoErrors();

        // Assert lead exists in database
        $this->assertDatabaseHas('seeding_leads', [
            'customer_name' => 'Nguyễn Thị Khách',
            'customer_uid' => 'khach.nguyen.99',
            'phone' => '0987654321',
            'platform' => 'facebook',
            'status' => 'synced',
        ]);

        $lead = \App\Models\SeedingLead::first();
        $this->assertNotNull($lead->zenify_lead_id);
        $this->assertStringStartsWith('zen_', $lead->zenify_lead_id);
    }

    public function test_can_retry_failed_sync_from_leads_crm(): void
    {
        $lead = \App\Models\SeedingLead::create([
            'seeder_account_id' => $this->account->id,
            'customer_name' => 'Failed Customer',
            'platform' => 'facebook',
            'content' => 'Error sync check',
            'status' => 'failed',
            'sync_error' => 'API Endpoint down',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Seeding\SeedingLeads::class)
            ->call('syncLead', $lead->id)
            ->assertHasNoErrors();

        $lead->refresh();
        $this->assertEquals('synced', $lead->status);
        $this->assertNull($lead->sync_error);
    }

    public function test_can_delete_lead_from_leads_crm(): void
    {
        $lead = \App\Models\SeedingLead::create([
            'seeder_account_id' => $this->account->id,
            'customer_name' => 'To Be Deleted',
            'platform' => 'facebook',
            'status' => 'new',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Seeding\SeedingLeads::class)
            ->call('deleteLead', $lead->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('seeding_leads', [
            'id' => $lead->id,
        ]);
    }

    public function test_zenify_sync_service_real_api_mock_http_fake(): void
    {
        config(['services.zenify.key' => 'actual-api-key-123']);
        config(['services.zenify.url' => 'https://api.zenify.cx/v1/leads']);

        \Illuminate\Support\Facades\Http::fake([
            'https://api.zenify.cx/*' => \Illuminate\Support\Facades\Http::response([
                'id' => 'zen_response_abc123',
                'success' => true
            ], 200)
        ]);

        $lead = \App\Models\SeedingLead::create([
            'seeder_account_id' => $this->account->id,
            'customer_name' => 'API Target Customer',
            'platform' => 'instagram',
            'content' => 'Interested in buying',
            'status' => 'new',
        ]);

        $syncSuccess = $lead->syncToZenify();
        $this->assertTrue($syncSuccess);

        $lead->refresh();
        $this->assertEquals('synced', $lead->status);
        $this->assertEquals('zen_response_abc123', $lead->zenify_lead_id);
        $this->assertNull($lead->sync_error);

        // Verify POST request format
        \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($lead) {
            return $request->url() === 'https://api.zenify.cx/v1/leads' &&
                $request->hasHeader('Authorization', 'Bearer actual-api-key-123') &&
                $request['name'] === 'API Target Customer' &&
                $request['source'] === 'Seeding Instagram' &&
                $request['external_id'] === (string) $lead->id;
        });
    }
}


