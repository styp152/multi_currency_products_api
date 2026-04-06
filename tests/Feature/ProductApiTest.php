<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $baseUrl = '/api/v1';

    public function test_it_lists_products_with_base_currency_and_prices(): void
    {
        $baseCurrency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
        ]);
        $secondaryCurrency = Currency::factory()->create([
            'name' => 'COP',
            'symbol' => '$',
            'exchange_rate' => 3900,
        ]);

        $product = Product::factory()->create([
            'currency_id' => $baseCurrency->id,
        ]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $secondaryCurrency->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $product->id)
            ->assertJsonPath('data.0.base_currency.name', 'USD')
            ->assertJsonPath('data.0.prices.0.is_base_price', true)
            ->assertJsonPath('data.0.prices.0.currency.name', 'USD')
            ->assertJsonPath('data.0.prices.1.currency.name', 'COP')
            ->assertJsonPath('data.0.prices.1.is_base_price', false);
    }

    public function test_it_creates_a_product(): void
    {
        $currency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1,
        ]);

        $payload = [
            'name' => 'Premium Coffee',
            'description' => 'Single origin coffee beans.',
            'price' => 29.99,
            'currency_id' => $currency->id,
            'tax_cost' => 3.50,
            'manufacturing_cost' => 12.10,
        ];

        $response = $this->postJson("{$this->baseUrl}/products", $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Premium Coffee')
            ->assertJsonPath('data.base_currency.name', 'USD')
            ->assertJsonPath('data.prices.0.price', 29.99)
            ->assertJsonPath('data.prices.0.currency_id', $currency->id)
            ->assertJsonPath('data.prices.0.is_base_price', true);

        $this->assertDatabaseHas('products', [
            'name' => 'Premium Coffee',
            'currency_id' => $currency->id,
        ]);
    }

    public function test_it_shows_a_single_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.prices.0.price', (float) $product->price)
            ->assertJsonPath('data.prices.0.currency_id', $currency->id)
            ->assertJsonPath('data.prices.0.is_base_price', true);
    }

    public function test_it_includes_base_price_as_the_first_item_in_the_price_collection_contract(): void
    {
        $baseCurrency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
        ]);
        $product = Product::factory()->create([
            'currency_id' => $baseCurrency->id,
            'price' => 24.5,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products/{$product->id}/prices");

        $response->assertOk()
            ->assertJsonPath('data.0.product_id', $product->id)
            ->assertJsonPath('data.0.price', 24.5)
            ->assertJsonPath('data.0.currency.name', 'USD')
            ->assertJsonPath('data.0.is_base_price', true);
    }

    public function test_it_updates_a_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'currency_id' => $currency->id,
            'name' => 'Old Name',
        ]);

        $response = $this->putJson("{$this->baseUrl}/products/{$product->id}", [
            'name' => 'New Name',
            'tax_cost' => 15,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.tax_cost', 15)
            ->assertJsonPath('data.prices.0.price', (float) $product->fresh()->price)
            ->assertJsonPath('data.prices.0.currency_id', $product->fresh()->currency_id)
            ->assertJsonPath('data.prices.0.is_base_price', true);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("{$this->baseUrl}/products/{$product->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_it_lists_product_prices(): void
    {
        $product = Product::factory()->create();
        $currency = Currency::factory()->create();

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'price' => 55.25,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products/{$product->id}/prices");

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.is_base_price', true)
            ->assertJsonPath('data.1.price', 55.25)
            ->assertJsonPath('data.1.is_base_price', false);
    }

    public function test_it_creates_a_product_price_for_a_specific_currency(): void
    {
        $product = Product::factory()->create();
        $currency = Currency::factory()->create([
            'name' => 'EUR',
            'symbol' => 'EUR',
        ]);

        $response = $this->postJson("{$this->baseUrl}/products/{$product->id}/prices", [
            'currency_id' => $currency->id,
            'price' => 42.90,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.currency.name', 'EUR')
            ->assertJsonPath('data.price', 42.9);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);
    }

    public function test_it_filters_and_paginates_products(): void
    {
        $usd = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
        ]);
        $eur = Currency::factory()->create([
            'name' => 'EUR',
            'symbol' => 'EUR',
        ]);

        Product::factory()->create([
            'name' => 'Alpha Coffee',
            'description' => 'Filtered result',
            'price' => 25,
            'currency_id' => $usd->id,
        ]);

        Product::factory()->create([
            'name' => 'Zulu Tea',
            'description' => 'Should not appear',
            'price' => 80,
            'currency_id' => $eur->id,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products?search=Coffee&currency_id={$usd->id}&sort_by=name&sort_direction=asc&per_page=1");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alpha Coffee')
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_it_returns_validation_errors_when_creating_a_product(): void
    {
        $response = $this->postJson("{$this->baseUrl}/products", []);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'validation_error')
            ->assertHeader('X-Request-Id')
            ->assertJsonStructure([
                'message',
                'code',
                'request_id',
                'errors' => [
                    'name',
                    'description',
                    'price',
                    'currency_id',
                    'tax_cost',
                    'manufacturing_cost',
                ],
            ]);
    }

    public function test_it_rejects_write_operations_without_a_valid_api_key(): void
    {
        $currency = Currency::factory()->create();

        $response = $this
            ->withoutHeader('X-API-Key')
            ->postJson("{$this->baseUrl}/products", [
                'name' => 'Blocked Product',
                'description' => 'Should not be created.',
                'price' => 10,
                'currency_id' => $currency->id,
                'tax_cost' => 1,
                'manufacturing_cost' => 2,
            ]);

        $response->assertStatus(401)
            ->assertJsonPath('code', 'unauthorized')
            ->assertJsonPath('message', 'The provided API key is invalid.');
    }

    public function test_it_allows_read_operations_without_an_api_key(): void
    {
        Product::factory()->create();

        $response = $this
            ->withoutHeader('X-API-Key')
            ->getJson("{$this->baseUrl}/products");

        $response->assertOk();
    }

    public function test_it_rejects_duplicate_price_currency_for_a_product(): void
    {
        $product = Product::factory()->create();
        $currency = Currency::factory()->create();

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);

        $response = $this->postJson("{$this->baseUrl}/products/{$product->id}/prices", [
            'currency_id' => $currency->id,
            'price' => 77.10,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'validation_error')
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_it_rejects_using_the_product_base_currency_as_an_additional_price(): void
    {
        $baseCurrency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
        ]);
        $product = Product::factory()->create([
            'currency_id' => $baseCurrency->id,
        ]);

        $response = $this->postJson("{$this->baseUrl}/products/{$product->id}/prices", [
            'currency_id' => $baseCurrency->id,
            'price' => 99.99,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'validation_error')
            ->assertJsonValidationErrors(['currency_id']);
    }

    public function test_it_rejects_duplicating_the_exact_base_price_in_additional_prices(): void
    {
        $baseCurrency = Currency::factory()->create();
        $product = Product::factory()->create([
            'currency_id' => $baseCurrency->id,
            'price' => 88.50,
        ]);

        $response = $this->postJson("{$this->baseUrl}/products/{$product->id}/prices", [
            'currency_id' => $baseCurrency->id,
            'price' => 88.50,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'validation_error')
            ->assertJsonValidationErrors(['currency_id', 'price']);
    }

    public function test_it_returns_json_when_a_product_is_not_found(): void
    {
        $response = $this->getJson("{$this->baseUrl}/products/999999");

        $response->assertStatus(404)
            ->assertJsonPath('code', 'resource_not_found')
            ->assertJsonPath('message', 'The requested resource was not found.');
    }

    public function test_it_filters_product_prices(): void
    {
        $baseCurrency = Currency::factory()->create([
            'name' => 'USD',
            'symbol' => '$',
        ]);
        $product = Product::factory()->create([
            'currency_id' => $baseCurrency->id,
            'price' => 39.99,
        ]);
        $usd = Currency::factory()->create();
        $eur = Currency::factory()->create();

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $usd->id,
            'price' => 40,
        ]);

        ProductPrice::factory()->create([
            'product_id' => $product->id,
            'currency_id' => $eur->id,
            'price' => 41,
        ]);

        $response = $this->getJson("{$this->baseUrl}/products/{$product->id}/prices?currency_id={$eur->id}&per_page=1");

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.is_base_price', true)
            ->assertJsonPath('data.0.currency_id', $baseCurrency->id)
            ->assertJsonPath('data.0.price', 39.99)
            ->assertJsonPath('data.1.currency.name', $eur->name)
            ->assertJsonPath('data.1.is_base_price', false)
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_it_applies_rate_limiting_to_the_api(): void
    {
        for ($attempt = 0; $attempt < 60; $attempt++) {
            $this->getJson("{$this->baseUrl}/products")->assertOk();
        }

        $this->getJson("{$this->baseUrl}/products")
            ->assertStatus(429);
    }

    public function test_it_preserves_a_client_supplied_request_id(): void
    {
        $response = $this
            ->withHeader('X-Request-Id', 'integration-test-request-id')
            ->getJson("{$this->baseUrl}/products");

        $response->assertOk()
            ->assertHeader('X-Request-Id', 'integration-test-request-id');
    }
}
