<?php
namespace Diagro\Webhooks\Client\Middleware;

use Closure;
use Diagro\Token\BackendApplicationToken;
use Exception;
use Illuminate\Http\Request;

/**
 * Checks if the X-BACKEND-TOKEN is precense and if it decodes.
 * This is used for webhooks and other communications between backends.
 *
 * @package Diagro\Backend\Middleware
 */
class BackendAppId
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->hasHeader('x-backend-token'), 500, "No backend token found!");

        try {
            BackendApplicationToken::createFromToken($request->header('x-backend-token'));
        } catch(Exception $e) {
            abort(500, "Invalid backend token!");
        }

        return $next($request);
    }


}
