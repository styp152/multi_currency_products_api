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

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $product->id)
            ->assertJsonPath('data.0.base_currency.name', 'USD')
            ->assertJsonPath('data.0.prices.0.currency.name', 'COP');
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

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Premium Coffee')
            ->assertJsonPath('data.base_currency.name', 'USD');

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

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_it_updates_a_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'currency_id' => $currency->id,
            'name' => 'Old Name',
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'New Name',
            'tax_cost' => 15,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.tax_cost', 15);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

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

        $response = $this->getJson("/api/products/{$product->id}/prices");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.price', 55.25);
    }

    public function test_it_creates_a_product_price_for_a_specific_currency(): void
    {
        $product = Product::factory()->create();
        $currency = Currency::factory()->create([
            'name' => 'EUR',
            'symbol' => 'EUR',
        ]);

        $response = $this->postJson("/api/products/{$product->id}/prices", [
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
}
