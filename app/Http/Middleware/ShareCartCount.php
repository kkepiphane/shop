<?php

namespace App\Http\Middleware;

use Closure;

class ShareCartCount
{
    public function handle($request, Closure $next)
    {
        view()->share('cartCount', \Cart::getTotalQuantity());
        return $next($request);
    }
}
