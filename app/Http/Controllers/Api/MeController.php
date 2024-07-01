<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class MeController extends Controller
{
    public function user(Request $request)
    {
        $user = auth('sanctum')->user();
        $hasBearer = (bool) $request->bearerToken();
        $isInvalidBearer = ! $user && $hasBearer;

        if ($isInvalidBearer) {
            throw ValidationException::withMessages(['The provided credentials are incorrect.']);
        }

        return response([
            'user' => $user,
        ]);
    }

    public function cart(Request $request)
    {
        $user = auth('sanctum')->user();

        if ($user) {
            return $this->getCartResponse('user_id', $user->id);
        }

        $anonymousId = $request->headers->get('X-Anonymous-Id');
        if (! $anonymousId) {
            throw ValidationException::withMessages(['The provided credentials are incorrect.']);
        }

        return $this->getCartResponse('anonymous_id', $anonymousId);
    }

    protected function getCartResponse(string $identifierKey, $identifierValue): Response
    {
        $cart = Cart::query()->firstOrCreate([
            $identifierKey => $identifierValue,
        ]);

        return response([
            'cart' => new CartResource($cart->fresh()),
        ]);
    }
}
