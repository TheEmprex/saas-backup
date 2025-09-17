<?php

declare(strict_types=1);

namespace Wave\Http\Middleware;

use Closure;

class ThemeDemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (property_exists($request, 'theme') && $request->theme !== null) {
            return redirect('/')->withCookie(cookie('theme', $request->theme, 60, null, null, false, false));
        }

        return $next($request);
    }
}
