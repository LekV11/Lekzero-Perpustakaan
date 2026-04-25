<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\JwtHelper;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class WebAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => 'user', // default role
                    'password' => bcrypt(Str::random(16)),
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // Generate JWT Token (mimic API login)
            $token = JwtHelper::create($user);
            session(['token' => $token]);
            auth()->login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login: ' . $e->getMessage()]);
        }
    }

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

        $internal = \Illuminate\Http\Request::create(route('api.login'), 'POST', $data, [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = app()->handle($internal);
        $status = $response->getStatusCode();
        $body = json_decode($response->getContent(), true);

        if ($status >= 400) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $token = $body['token'] ?? null;
        session(['token' => $token]);

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
