<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin {

    use ApiResponser;

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

        return $this->errorResponse( 'Http forbidden.', Response::HTTP_FORBIDDEN );
    }
}
