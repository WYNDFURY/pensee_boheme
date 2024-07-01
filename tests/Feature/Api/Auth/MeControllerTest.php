<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_route_returns_null_user_for_unauthenticated_requests()
    {
        $route = route('api.me.user');
        $response = $this->getJson($route);
        $response->assertOk();
        $user = $response->json('user');

        $this->assertNull($user);
    }

    public function test_user_route_returns_user_for_authenticated_requests()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;
        $route = route('api.me.user');
        $response = $this->getJson($route, [
            'Authorization' => "Bearer $token",
        ]);
        $response->assertOk();
        $user = $response->json('user');

        $this->assertNotNull($user);
    }

    public function test_user_route_throws_for_invalid_tokens()
    {
        User::factory()->create();

        $route = route('api.me.user');
        $response = $this->getJson($route, [
            'Authorization' => 'Bearer expired',
        ]);
        $response->assertUnprocessable();
    }

    public function test_cart_route_throws_for_unauthenticated_requests_without_anonymous_id()
    {
        $route = route('api.me.cart');
        $response = $this->getJson($route);
        $response->assertUnprocessable();
    }

    public function test_cart_route_returns_new_cart_for_unauthenticated_requests_with_anonymous_id()
    {
        $uuid = Str::uuid()->toString();
        $route = route('api.me.cart');
        $response = $this->getJson($route, ['X-Anonymous-Id' => $uuid]);

        $response->assertOk();
        $cart = $response->json('cart');

        $this->assertNotNull($cart);
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'anonymous_id' => $uuid,
        ]);
    }

    public function test_cart_route_returns_existing_cart_for_unauthenticated_requests_with_anonymous_id()
    {
        $uuid = Str::uuid()->toString();
        $route = route('api.me.cart');
        Cart::factory()->create(['anonymous_id' => $uuid]);
        $response = $this->getJson($route, ['X-Anonymous-Id' => $uuid]);

        $response->assertOk();
        $cart = $response->json('cart');

        $this->assertNotNull($cart);
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'anonymous_id' => $uuid,
        ]);
    }

    public function test_cart_route_returns_new_cart_for_authenticated_requests()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $route = route('api.me.cart');
        $response = $this->getJson($route, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $cart = $response->json('cart');

        $this->assertNotNull($cart);
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
    }

    public function test_cart_route_returns_existing_cart_for_authenticated_requests()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)
            ->hasAttached(
                Product::factory()->count(5),
                ['quantity' => 5]
            )->create();

        $token = $user->createToken('test')->plainTextToken;
        $route = route('api.me.cart');
        $response = $this->getJson($route, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $cart = $response->json('cart');

        $this->assertNotNull($cart);
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
    }
}
