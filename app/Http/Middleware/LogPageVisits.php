<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogPageVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if(Auth::check()) {
            if($request->isMethod('get')) {
                if(!$request->is('assets/*') && !$request->is('storage/*') && !$request->is('images/*') && !$request->is('css/*') && !$request->is('js/*')) {
                    logActivity(
                        'زيارة صفحة', 
                        "قام " . Auth::user()->name . " بزيارة الصفحة: " . $request->fullUrl()
                    );
                }
            }
        }

        return $response;
    }
}
