<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\JwtHelper;

class JwtSession
{
    /**
     * Handle an incoming request.
     * Ensure a valid JWT token exists in session and authenticate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('token')) {
            return redirect()->route('login');
        }

        try {
            $token = session('token');
            $payload = JwtHelper::decode($token);
            $user = \App\Models\User::find($payload['sub']);

            if ($user) {
                auth()->login($user);
            } else {
                throw new \Exception('User not found');
            }
        } catch (\Exception $e) {
            // token invalid, expired, or user missing
            session()->forget('token');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
