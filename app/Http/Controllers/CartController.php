<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->cart === null) {
            $cart = new Cart();
            $cart->user_id = $request->user()->id;
            $cart->save();
            return response()->json($cart, 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Cart::find($id) === null) {
            return response()->json(null, 404);
        }
        return response()->json(Cart::find($id), 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::find($id);
        $cart->delete();
        return response()->json(null, 204);
    }
}
