<?php
namespace Diagro\Webhooks\Client;

use Diagro\Backend\Console\Commands\BackendTokenGenerator;
use Diagro\Backend\Middleware\AppIdValidate;
use Diagro\Backend\Middleware\AuthorizedApplication;
use Diagro\Backend\Middleware\BackendAppIdValidate;
use Diagro\Backend\Middleware\CacheResource;
use Diagro\Backend\Middleware\TokenValidate;
use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Token\Auth\TokenProvider;
use Diagro\Webhooks\Client\Middleware\BackendAppId;
use Exception;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Bridge between package and laravel backend application.
 *
 * @package Diagro\Backend
 */
class DiagroServiceProvider extends ServiceProvider
{


    public function register()
    {
    }


    /**
     * Boot me up Scotty!
     */
    public function boot(Kernel $kernel)
    {
        //middleware
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('webhooks', BackendAppId::class);
        $kernel->prependToMiddlewarePriority(BackendAppId::class);

        //routes
        $this->loadRoutesFrom(__DIR__ .' ../routes/webhooks.php');

        //commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                //publish webhook configs to the backend servers
                // at endpoint POST /webhooks/register {url, signing_secret}
                //can be used in init.sh for dockers: php artisan diagro:websockets publish (--name=....)
            ]);
        }
    }


}