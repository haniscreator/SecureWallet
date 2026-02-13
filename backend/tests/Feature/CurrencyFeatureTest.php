<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\User\Models\UserRole;
use App\Domain\Currency\Models\Currency;

class CurrencyFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles
        if (UserRole::count() === 0) {
            UserRole::create(['name' => 'admin', 'label' => 'Admin']);
            UserRole::create(['name' => 'manager', 'label' => 'Manager']);
            UserRole::create(['name' => 'user', 'label' => 'User']);
        }
    }

    public function test_admin_can_update_currency_code()
    {
        $adminRole = UserRole::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $currency = Currency::factory()->create([
            'code' => 'TST',
            'name' => 'Test Currency',
            'symbol' => 'T'
        ]);

        $response = $this->actingAs($admin)->putJson("/api/currencies/{$currency->id}", [
            'code' => 'UPD',
            'name' => 'Updated Currency',
            'symbol' => 'U'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'code' => 'UPD',
            'name' => 'Updated Currency'
        ]);
    }

    public function test_admin_can_update_currency_without_changing_code()
    {
        $adminRole = UserRole::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $currency = Currency::factory()->create([
            'code' => 'SAME',
            'name' => 'Original Name'
        ]);

        $response = $this->actingAs($admin)->putJson("/api/currencies/{$currency->id}", [
            'code' => 'SAME',
            'name' => 'New Name'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            'code' => 'SAME',
            'name' => 'New Name'
        ]);
    }

    public function test_admin_can_delete_currency()
    {
        $adminRole = UserRole::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $currency = Currency::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/currencies/{$currency->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_non_admin_cannot_delete_currency()
    {
        $userRole = UserRole::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $currency = Currency::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/currencies/{$currency->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
    }
}
