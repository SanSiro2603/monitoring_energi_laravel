<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Response as LaravelResponse;

class NoCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Tambahkan header hanya jika responsenya adalah Laravel Response biasa
        if ($response instanceof LaravelResponse) {
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                     ->header('Pragma', 'no-cache')
                     ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}


