<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOfAge
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to participate.');
        }

        if (!auth()->user()->isOfAge()) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be 18 or older to participate in the contest.');
        }

        return $next($request);
    }
}
