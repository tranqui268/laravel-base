<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotLoggedIn
{

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/')->withErrors(['email' => 'You must be logged in to access this page.']);
        }

        return $next($request);
    }
}
