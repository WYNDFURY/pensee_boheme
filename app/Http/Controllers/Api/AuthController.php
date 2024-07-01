<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var ?User */
        $user = User::query()->where('email', $data['email'])->first();
        $isAuthenticable = $user && Hash::check($data['password'], $user->password);

        if (!$isAuthenticable) {
            throw ValidationException::withMessages(['The provided credentials are incorrect.']);
        }

        $token = $user->createToken('personal access token')
            ->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string'],
        ]);

        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }
}
