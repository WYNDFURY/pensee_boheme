<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 users
        User::factory(10)->create()->each(function ($user) {

            // Each user has 1 cart with 1 to 5 items
            $cart = Cart::factory()->create(['user_id' => $user->id]);
            CartItem::factory(rand(1, 5))->create(['cart_id' => $cart->id]);

            // Each user makes 1 to 3 orders
            for ($i = 0; $i < rand(1, 3); $i++) {
                $order = Order::factory()->create(['user_id' => $user->id]);
                // Each order has 1 to 5 order items
                OrderItem::factory(rand(1, 5))->create(['order_id' => $order->id]);
            }

            // Each user leaves 1 to 5 reviews on random products
            Review::factory(rand(1, 5))->create(['user_id' => $user->id]);
        });

        // Create 5 categories
        Category::factory(5)->create()->each(function ($category) {
            // Each category has 5 to 10 products
            Product::factory(rand(5, 10))->create(['category_id' => $category->id]);
        });
    }
}
