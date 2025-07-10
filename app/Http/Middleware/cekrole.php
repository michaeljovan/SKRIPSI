<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class cekrole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$role): Response
    {
        if(!in_array($request->user()->role,$role)){
            return redirect()->back()->withErrors(['errors','Anda tidak memiliki akses']);
        }
        return $next($request);
    }
}
