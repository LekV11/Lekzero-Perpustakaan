<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\JwtHelper;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // dispatch an internal request to the API login route
        $internal = \Illuminate\Http\Request::create(route('api.login'), 'POST', $data, [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = app()->handle($internal);
        $status = $response->getStatusCode();
        $body = json_decode($response->getContent(), true);

        if ($status >= 400) {
            // mimic previous error message
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $token = $body['token'] ?? null;
        session(['token' => $token]);

        // authenticate user in local session by decoding token
        try {
            $payload = JwtHelper::decode($token);
            $user = \App\Models\User::find($payload['sub']);
            if ($user) {
                auth()->login($user);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return redirect()->intended(route('dashboard'));

    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        // dispatch internal request to API register instead of going through curl
        $internal = \Illuminate\Http\Request::create(
            route('api.register'),
            'POST',
            array_merge($data, ['password_confirmation' => $data['password']]),
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        );
        $response = app()->handle($internal);
        $status = $response->getStatusCode();
        $body = json_decode($response->getContent(), true);

        if ($status >= 400) {
            // log for debugging
            \Log::error('Register API failed', [
                'status' => $status,
                'body' => $response->getContent(),
            ]);

            // forward validation info if available
            $errors = [];
            if (isset($body['errors']) && is_array($body['errors'])) {
                foreach ($body['errors'] as $field => $msgs) {
                    $errors[$field] = is_array($msgs) ? implode(' ', $msgs) : $msgs;
                }
            } elseif (isset($body['message'])) {
                $errors['email'] = $body['message'];
            } else {
                $errors['email'] = 'Unable to register';
            }
            return back()->withErrors($errors)->withInput();
        }

        $token = $body['token'] ?? null;
        session(['token' => $token]);

        // login user
        try {
            $payload = JwtHelper::decode($token);
            $user = \App\Models\User::find($payload['sub']);
            if ($user) {
                auth()->login($user);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        if (session()->has('token')) {
            // no real invalidation, just remove
            session()->forget('token');
        }
        session()->forget('token');
        auth()->logout();
        return redirect()->route('login');
    }
}
