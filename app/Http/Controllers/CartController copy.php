<?php

namespace App\Http\Controllers;

use App\Http\Services\Cart\CartService;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartService::getCartItems();

        $ids = Arr::pluck($cartItems, 'product_id');
        $products = Product::query()->whereIn('id', $ids)->get();
        $cartItems = Arr::keyBy($cartItems, 'product_id');
        $total = 0;

        foreach ($products as $product) {
            $product->quantity = $cartItems[$product->id]['quantity'];
            $total += $product->price * $product->quantity;
        }

        return view('cart.index', compact('products', 'total'));
    }

    public function store(Request $request, Product $product)
    {
        $quantity = $request->post('quantity', 1);
        $user = $request->user();

        if ($user) {
            $cartItem = CartItem::where(['cart_id' => $user->cart->id, 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->update();
            } else {
                $data = [
                    'cart_id' => $user->cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];
                CartItem::create($data);
            }

            return response([
                'count' => CartService::getCartItemsCount()
            ]);
        } else {

            $cartItems = CartService::getCookieCartItems();
        }
    }
}
