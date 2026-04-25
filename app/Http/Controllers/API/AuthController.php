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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'sometimes|string|in:admin,user',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 60*60*24*7, // Extended to 7 days for mobile
        ];
        $token = JwtHelper::encode($payload);

        return $this->sendResponse(['user' => $user, 'token' => $token], 'User registered successfully.', 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! auth()->attempt($credentials)) {
            return $this->sendError('Invalid credentials.', ['error' => 'Unauthorised'], 401);
        }

        $user = auth()->user();
        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 60*60*24*7, // Extended to 7 days for mobile
        ];
        $token = JwtHelper::encode($payload);

        return $this->sendResponse(['token' => $token, 'user' => $user], 'User logged in successfully.');
    }

    public function me(Request $request)
    {
        return $this->sendResponse(auth()->user(), 'User profile retrieved successfully.');
    }

    public function logout(Request $request)
    {
        return $this->sendResponse([], 'Successfully logged out.');
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
