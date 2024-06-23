<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Tokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = session('api_token');
        if (!$token) {
            return back()->with('error', 'Silahkan Login!');
        }
        $user = User::where('remember_token', '=', $token)->first();
        if ($user) {
            Auth::login($user);
        }
        return $next($request);
    }

}
