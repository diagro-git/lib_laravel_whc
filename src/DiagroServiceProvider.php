<?php
namespace Diagro\Webhooks\Client;

use Diagro\Webhooks\Client\Commands\Register;
use Diagro\Webhooks\Client\Commands\Unregister;
use Diagro\Webhooks\Client\Middleware\BackendAppId;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
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
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhooks.php');

        //commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Register::class,
                Unregister::class,
            ]);
        }
    }


}