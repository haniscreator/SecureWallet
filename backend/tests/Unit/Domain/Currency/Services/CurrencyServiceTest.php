<?php

namespace Tests\Unit\Domain\Currency\Services;

use Tests\TestCase;
use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CurrencyService $currencyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyService = new CurrencyService();
    }

    public function test_create_currency()
    {
        $data = [
            'code' => 'TST',
            'name' => 'Test Coin',
            'symbol' => 'T',
            'status' => 1,
        ];

        $currency = $this->currencyService->create($data);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('TST', $currency->code);
        $this->assertDatabaseHas('currencies', ['code' => 'TST']);
    }

    public function test_create_currency_defaults_status_to_active()
    {
        $data = [
            'code' => 'ACT',
            'name' => 'Active Coin',
            'symbol' => 'A',
        ];

        $currency = $this->currencyService->create($data);

        $this->assertEquals(1, $currency->status); // 1 is active (true cast handled by model)
    }

    public function test_list_currencies()
    {
        Currency::create(['code' => 'C1', 'name' => 'C1', 'symbol' => '1']);
        Currency::create(['code' => 'C2', 'name' => 'C2', 'symbol' => '2']);

        $currencies = $this->currencyService->listCurrencies();

        $this->assertGreaterThanOrEqual(2, $currencies->count());
    }

    public function test_get_currency()
    {
        $currency = Currency::create(['code' => 'FND', 'name' => 'Find Me', 'symbol' => 'F']);

        $found = $this->currencyService->getCurrency($currency->id);

        $this->assertEquals($currency->id, $found->id);
    }

    public function test_update_currency()
    {
        $currency = Currency::create(['code' => 'OLD', 'name' => 'Old Name', 'symbol' => 'O']);

        $updated = $this->currencyService->update($currency, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('currencies', ['id' => $currency->id, 'name' => 'New Name']);
    }

    public function test_delete_currency()
    {
        $currency = Currency::create(['code' => 'DEL', 'name' => 'Delete Me', 'symbol' => 'D']);

        $this->currencyService->delete($currency);

        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }
}
