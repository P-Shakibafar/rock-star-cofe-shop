<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function route;
use function redirect;

class IsAdmin {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle( Request $request, Closure $next )
    {
        if( Auth::user() && ( Auth::user()->is_admin ) ) {
            return $next( $request );
        }

        return redirect( route( 'login' ) );
    }
}
