<?php

namespace Tests\Unit\Domain\Currency\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Currency\Services\CurrencyService;
use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Actions\CreateCurrencyAction;
use App\Domain\Currency\Actions\UpdateCurrencyAction;
use App\Domain\Currency\Actions\DeleteCurrencyAction;
use Mockery;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Currency::query()->delete();
    }

    public function test_list_currencies()
    {
        Currency::create(['code' => 'USD_LIST', 'name' => 'US Dollar', 'symbol' => '$', 'status' => 1]);
        Currency::create(['code' => 'EUR_LIST', 'name' => 'Euro', 'symbol' => '€', 'status' => 1]);

        $createAction = Mockery::mock(CreateCurrencyAction::class);
        $updateAction = Mockery::mock(UpdateCurrencyAction::class);
        $deleteAction = Mockery::mock(DeleteCurrencyAction::class);

        $service = new CurrencyService($createAction, $updateAction, $deleteAction);

        $currencies = $service->listCurrencies();

        $this->assertCount(2, $currencies);
    }

    public function test_create_currency_delegates_to_action()
    {
        $data = ['code' => 'GBP_NEW', 'name' => 'British Pound'];
        $currency = new Currency($data);

        $createAction = Mockery::mock(CreateCurrencyAction::class);
        $createAction->shouldReceive('execute')
            ->once()
            ->with($data)
            ->andReturn($currency);

        $updateAction = Mockery::mock(UpdateCurrencyAction::class);
        $deleteAction = Mockery::mock(DeleteCurrencyAction::class);

        $service = new CurrencyService($createAction, $updateAction, $deleteAction);

        $result = $service->createCurrency($data);

        $this->assertEquals($currency, $result);
    }

    public function test_update_currency_delegates_to_action()
    {
        $currency = Currency::create(['code' => 'USD_UPD', 'name' => 'US Dollar', 'symbol' => '$']);
        $data = ['name' => 'Updated Name'];
        $updatedCurrency = $currency->replicate()->fill($data);

        $createAction = Mockery::mock(CreateCurrencyAction::class);
        $updateAction = Mockery::mock(UpdateCurrencyAction::class);
        $updateAction->shouldReceive('execute')
            ->once()
            ->with($currency, $data)
            ->andReturn($updatedCurrency);

        $deleteAction = Mockery::mock(DeleteCurrencyAction::class);

        $service = new CurrencyService($createAction, $updateAction, $deleteAction);

        $result = $service->updateCurrency($currency, $data);

        $this->assertEquals($updatedCurrency, $result);
    }

    public function test_delete_currency_delegates_to_action()
    {
        $currency = Currency::create(['code' => 'USD_DEL', 'name' => 'US Dollar', 'symbol' => '$']);

        $createAction = Mockery::mock(CreateCurrencyAction::class);
        $updateAction = Mockery::mock(UpdateCurrencyAction::class);
        $deleteAction = Mockery::mock(DeleteCurrencyAction::class);
        $deleteAction->shouldReceive('execute')
            ->once()
            ->with($currency);

        $service = new CurrencyService($createAction, $updateAction, $deleteAction);

        $service->deleteCurrency($currency);
    }
}
