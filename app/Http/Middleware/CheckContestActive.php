<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckContestActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $contestActive = Setting::getValue('contest_active', true);

        if (!$contestActive) {
            return redirect()->route('home')
                ->with('info', 'The contest is currently paused. Please check back later.');
        }

        return $next($request);
    }
}
