<?php

namespace App\Http\Middleware;

use Closure;

class BannerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (setting('store_banner', \App\Domain\Banner\Models\Banner::SHOW) == \App\Domain\Banner\Models\Banner::SHOW) {
            return $next($request);
        }
        abort(403);
    }
}
