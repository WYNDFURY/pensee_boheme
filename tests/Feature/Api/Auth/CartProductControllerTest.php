<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_user_can_update_cart_product_quantity()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'anonymous_id' => $uuid,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.update', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->putJson($route, [
            'quantity' => 2,
        ], [
            'x-anonymous-id' => $uuid,
        ]);

        $response->assertOk();
        $this->assertEquals(2, $cart->fresh()->products->first()->pivot->quantity);
    }

    public function test_authenticated_user_can_update_cart_product_quantity()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'user_id' => $user->id,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.update', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->putJson($route, [
            'quantity' => 2,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $this->assertEquals(2, $cart->fresh()->products->first()->pivot->quantity);
    }

    public function test_unanymous_user_can_update_cart_product_quantity_to_zero()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'anonymous_id' => $uuid,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.update', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->putJson($route, [
            'quantity' => 0,
        ], [
            'x-anonymous-id' => $uuid,
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_can_update_cart_product_quantity_to_zero()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'user_id' => $user->id,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.update', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->putJson($route, [
            'quantity' => 0,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_anonymous_user_can_remove_cart_product()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'anonymous_id' => $uuid,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.destroy', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->deleteJson($route, [], [
            'x-anonymous-id' => $uuid,
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_can_remove_cart_product()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 1]
            )
            ->create([
                'user_id' => $user->id,
            ]);
        $product = $cart->products->first();
        $route = route('api.carts.products.destroy', [
            'cart' => $cart->getRouteKey(),
            'product' => $product->getRouteKey(),
        ]);
        $response = $this->deleteJson($route, [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('cart_product', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }
}
