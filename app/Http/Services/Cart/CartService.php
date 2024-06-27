<?php

namespace App\Http\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
  public static function getCartItemsCount()
  {
    $request = request();
    $user = $request->user();

    if ($user) {
      return CartItem::where('cart_id', $user->cart->id)->sum('quantity');
    } else {
      $cartItems = Self::getCookieCartItems();

      return array_reduce(
        $cartItems,
        function ($carry, $item) {
          return $carry + $item['quantity'];
        },
        0
      );
    }
  }

  public static function getCartItems()
  {
    $request = request();
    $user = $request->user();

    if ($user) {
      return CartItem::where('cart_id', $user->cart->id)->get()->map(
        function ($item) {
          return [
            'cart_id' => $item->cart_id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
          ];
        }
      );
    } else {
      return Self::getCookieCartItems();
    }
  }


  public static function getCookieCartItems()
  {
    $request = request();

    return json_decode($request->cookie('cart_items', []), true);
  }

  public static function getCountFromItems($cartItems)
  {
    return array_reduce(
      $cartItems,
      function ($carry, $item) {
        return $carry + $item['quantity'];
      },
      0
    );
  }

  public static function moveCartItemsIntoDb()
  {
    $request = request();
    $cartItems = Self::getCookieCartItems();
    $dbCartItems = CartItem::where('cart_id', $request->user()->cart->id)->get();
    $newCartItems = [];
    foreach ($cartItems as $cartItem) {
      if (isset($dbCartItems[$cartItem['product_id']])) {
        continue;
      }
      $newCartItems[] = [
        'cart_id' => $request->user()->cart->id,
        'product_id' => $cartItem['product_id'],
        'quantity' => $cartItem['quantity'],
      ];
    }

    if (!empty($newCartItems)) {
      CartItem::insert($newCartItems);
    }
  }
}
