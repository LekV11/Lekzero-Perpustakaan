<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\JwtHelper;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'sometimes|string|in:admin,user',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);

        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 60*60*24, // 1 day
        ];
        $token = JwtHelper::encode($payload);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // manually validate credentials
        if (! auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();
        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 60*60*24,
        ];
        $token = JwtHelper::encode($payload);

        return response()->json(compact('token'));
    }

    public function me(Request $request)
    {
        // user already loaded by api.jwt middleware
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        // no server-side invalidation; client should discard token
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(Request $request)
    {
        $header = $request->header('Authorization');
        $token = substr($header, 7);
        try {
            $payload = JwtHelper::decode($token);
            // issue new token with extended expiry
            $payload['iat'] = time();
            $payload['exp'] = time() + 60*60*24;
            $new = JwtHelper::encode($payload);
            return response()->json(['token' => $new]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}
