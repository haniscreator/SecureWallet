<?php

namespace Tests\Unit\Domain\Wallet\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Wallet\Services\WalletService;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Actions\CreateWalletAction;
use App\Domain\Wallet\Actions\UpdateWalletStatusAction;
use App\Domain\Wallet\Actions\AssignWalletAction;
use Mockery;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_wallet_delegates_to_action()
    {
        $data = ['name' => 'Test Wallet', 'currency_id' => 1];
        $wallet = new Wallet($data);

        $createAction = Mockery::mock(CreateWalletAction::class);
        $createAction->shouldReceive('execute')
            ->once()
            ->with($data)
            ->andReturn($wallet);

        $updateStatusAction = Mockery::mock(UpdateWalletStatusAction::class);
        $assignAction = Mockery::mock(AssignWalletAction::class);

        $service = new WalletService($createAction, $updateStatusAction, $assignAction);

        $result = $service->createWallet($data);

        $this->assertEquals($wallet, $result);
    }

    public function test_list_wallets_admin_returns_all()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Wallet::create(['name' => 'W1', 'currency_id' => 1, 'status' => 1]);
        Wallet::create(['name' => 'W2', 'currency_id' => 1, 'status' => 1]);

        // Mocks not strictly needed for this method, but required for Constructor
        $createAction = Mockery::mock(CreateWalletAction::class);
        $updateStatusAction = Mockery::mock(UpdateWalletStatusAction::class);
        $assignAction = Mockery::mock(AssignWalletAction::class);

        $service = new WalletService($createAction, $updateStatusAction, $assignAction);

        $wallets = $service->listWallets($admin);

        $this->assertCount(2, $wallets);
    }

    public function test_list_wallets_user_returns_only_assigned()
    {
        $user = User::factory()->create(['role' => 'user']);
        $w1 = Wallet::create(['name' => 'W1', 'currency_id' => 1, 'status' => 1]);
        $w2 = Wallet::create(['name' => 'W2', 'currency_id' => 1, 'status' => 1]);

        $w1->users()->attach($user->id);

        $createAction = Mockery::mock(CreateWalletAction::class);
        $updateStatusAction = Mockery::mock(UpdateWalletStatusAction::class);
        $assignAction = Mockery::mock(AssignWalletAction::class);

        $service = new WalletService($createAction, $updateStatusAction, $assignAction);

        $wallets = $service->listWallets($user);

        $this->assertCount(1, $wallets);
        $this->assertEquals($w1->id, $wallets->first()->id);
    }
}
