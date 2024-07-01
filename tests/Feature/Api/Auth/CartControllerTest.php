<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_any_cart_without_credentials()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()->create([
            'anonymous_id' => $uuid,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route);

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_user_can_access_his_own_cart()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()->create([
            'anonymous_id' => $uuid,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'X-Anonymous-Id' => $uuid,
        ]);

        $response->assertOk();
    }

    public function test_unauthenticated_user_cannot_access_someone_else_cart()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()->create([
            'anonymous_id' => $uuid,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'X-Anonymous-Id' => 'another',
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_access_his_own_cart()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
    }

    public function test_authenticated_user_cannot_access_someone_else_cart()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_can_empty_his_own_cart()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()->hasAttached(Product::factory()->count(5), ['quantity' => 5])->create([
            'anonymous_id' => $uuid,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'X-Anonymous-Id' => $uuid,
        ]);

        $response->assertOk();
        $this->assertEmpty($cart->products()->count());
    }

    public function test_authenticated_user_can_empty_his_own_cart()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()->hasAttached(Product::factory()->count(5), ['quantity' => 5])->create([
            'user_id' => $user->id,
        ]);
        $route = route('api.carts.empty', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertOk();
        $this->assertEmpty($cart->products()->count());
    }

    public function test_freshly_registered_user_can_transfer_his_cart()
    {
        $uuid = Str::uuid()->toString();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $cart = Cart::factory()->create([
            'anonymous_id' => $uuid,
            'user_id' => null,
        ]);
        $route = route('api.carts.transfer', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'Authorization' => "Bearer $token",
            'X-Anonymous-Id' => $uuid,
        ]);

        $cart->refresh();

        $response->assertOk();
        $this->assertEquals($cart->user_id, $user->id);
        $this->assertNull($cart->anonymous_id);
    }

    public function test_unauthenticated_user_cannot_transfer_his_cart()
    {
        $uuid = Str::uuid()->toString();
        $cart = Cart::factory()->create([
            'anonymous_id' => $uuid,
            'user_id' => null,
        ]);
        $route = route('api.carts.transfer', [
            'cart' => $cart->getRouteKey(),
        ]);
        $response = $this->patchJson($route, [], [
            'X-Anonymous-Id' => $uuid,
        ]);

        $response->assertUnauthorized();
    }
}
