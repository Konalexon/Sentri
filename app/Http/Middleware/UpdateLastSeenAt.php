<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeenAt
{
    /**
     * Handle an incoming request.
     * Updates the user's last_seen_at timestamp.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            // Only update every 5 minutes to avoid excessive DB writes
            $lastSeen = $request->user()->last_seen_at;
            if (!$lastSeen || $lastSeen->diffInMinutes(now()) >= 5) {
                $request->user()->updateLastSeen();
            }
        }

        return $next($request);
    }
}
