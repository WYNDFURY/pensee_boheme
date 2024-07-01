<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_can_authenticate_himself(): void
    {
        $user = User::factory()->create();

        $route = route('api.auth.login');
        $response = $this->postJson($route, [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertOk();
    }

    public function test_user_can_authenticate_himself_and_access_protected_route_with_token_response(): void
    {
        $user = User::factory()->create();

        $route = route('api.auth.login');
        $response = $this->postJson($route, [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertOk();
        $token = $response->json('token');

        $userRoute = route('api.me.user');
        $userResponse = $this->getJson($userRoute, [
            'Authorization' => "Bearer $token",
        ]);
        $userResponse->assertOk();
        $user = $userResponse->json('user');

        $this->assertNotNull($user);
    }

    public function test_user_cannot_authenticate_himself_with_invalid_email(): void
    {
        $user = User::factory()->create();

        $route = route('api.auth.login');
        $response = $this->postJson($route, [
            'email' => 'invalid',
            'password' => 'password',
        ]);
        $response->assertUnprocessable();
    }

    public function test_user_cannot_authenticate_himself_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $route = route('api.auth.login');
        $response = $this->postJson($route, [
            'email' => $user->email,
            'password' => 'invalid',
        ]);
        $response->assertUnprocessable();
    }

    public function test_user_cannot_authenticate_himself_with_invalid_password_and_email(): void
    {
        User::factory()->create();

        $route = route('api.auth.login');
        $response = $this->postJson($route, [
            'email' => 'invalid',
            'password' => 'invalid',
        ]);
        $response->assertUnprocessable();
    }
}
