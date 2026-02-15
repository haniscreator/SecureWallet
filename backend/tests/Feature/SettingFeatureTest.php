<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Setting\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Domain\Transaction\Models\TransactionStatus::flushCache();
        // Seed settings
        Setting::create(['key' => 'transfer_limit', 'value' => '1000', 'type' => 'number']);
        // Seed statuses required for service logic
        TransactionStatus::firstOrCreate(['code' => 'pending'], ['name' => 'Pending']);
    }

    /** @test */
    public function admin_cannot_update_settings_when_pending_transactions_exist()
    {
        $adminRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Create pending status if not exists
        $pendingStatus = TransactionStatus::firstOrCreate(['name' => 'Pending', 'code' => 'pending']);

        // Create a pending transaction
        Transaction::factory()->create([
            'transaction_status_id' => $pendingStatus->id
        ]);

        $response = $this->actingAs($admin)->postJson('/api/settings', [
            'settings' => [
                ['key' => 'transfer_limit', 'value' => '2000']
            ]
        ]);

        $response->assertStatus(400) // Or 422, let's assume 400 for business logic error
            ->assertJson(['message' => 'Sorry, it is not able to update due to pending approval records.']);
    }

    /** @test */
    public function admin_can_update_settings_when_no_pending_transactions_exist()
    {
        $adminRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Ensure no pending transactions
        Transaction::whereHas('status', function ($q) {
            $q->where('code', 'pending');
        })->delete();

        $response = $this->actingAs($admin)->postJson('/api/settings', [
            'settings' => [
                ['key' => 'transfer_limit', 'value' => '5000']
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Settings updated successfully']);

        $this->assertDatabaseHas('settings', [
            'key' => 'transfer_limit',
            'value' => '5000'
        ]);
    }
}
