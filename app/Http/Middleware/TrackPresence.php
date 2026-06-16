<?php

namespace App\Http\Middleware;

use App\Services\PresenceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPresence
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            PresenceService::touch((int) $request->user()->id);
        }

        return $next($request);
    }
}
