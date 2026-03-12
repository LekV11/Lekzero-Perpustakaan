<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\JwtHelper;

class ApiJwt
{
    /**
     * Handle an incoming request.
     * Expect Authorization header Bearer token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = substr($header, 7);
        try {
            $payload = JwtHelper::decode($token);
            $user = \App\Models\User::find($payload['sub']);
            if (! $user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            // log in user for request
            auth()->login($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token: '.$e->getMessage()], 401);
        }

        return $next($request);
    }
}
