<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class HasAccessToCart
{
    public function handle(Request $request, Closure $next)
    {
        $cart = $request->route()->parameter('cart');
        $user = auth('sanctum')->user();
        $anonymousId = $request->header('X-Anonymous-Id');

        $lacksAuthentication = ! $user && ! $anonymousId;

        if ($lacksAuthentication) {
            return response([
                'message' => 'unauthenticated.',
            ], 401);
        }

        if ($this->authenticatedMissingAccess($cart, $user)) {
            return $this->forbidAccess();
        }

        if ($user) {
            return $next($request);
        }

        if ($this->anonymousMissingAccess($cart, $anonymousId)) {
            return $this->forbidAccess();
        }

        return $next($request);
    }

    protected function authenticatedMissingAccess(Cart $cart, ?User $user): bool
    {
        if (! $user || ! $cart->user_id) {
            return false;
        }

        return $cart->user_id !== $user->id;
    }

    protected function anonymousMissingAccess(Cart $cart, string $anonymousId): bool
    {
        if (! $anonymousId) {
            return true;
        }

        return $cart->anonymous_id !== $anonymousId;
    }

    protected function forbidAccess()
    {
        return response([
            'message' => 'You do not have access to this resource.',
        ], 403);
    }
}
