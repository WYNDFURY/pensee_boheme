<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;

class CartController extends Controller
{
    public function transfer(Cart $cart)
    {
        $user = auth()->user();

        $cart->user_id = $user->id;
        $cart->anonymous_id = null;
        $cart->save();

        return new CartResource($cart->fresh());
    }

    public function empty(Cart $cart)
    {
        $cart->products()->detach();

        return new CartResource($cart->fresh());
    }
}
