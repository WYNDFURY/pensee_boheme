<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class CartProductController extends Controller
{
    public function update(Cart $cart, Product $product, Request $request)
    {
        /** @var array{quantity: number} */
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $isEmpty = $data['quantity'] === 0;

        if ($isEmpty) {
            return $this->destroy($cart, $product);
        }

        $cartProduct = CartProduct::query()->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$cartProduct) {
            $cartProduct = new CartProduct([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);
        }

        $cartProduct->quantity = $data['quantity'];
        $cartProduct->save();

        return new CartResource($cart->fresh());
    }

    public function destroy(Cart $cart, Product $product)
    {
        CartProduct::query()->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->delete();

        return new CartResource($cart->fresh());
    }
}
