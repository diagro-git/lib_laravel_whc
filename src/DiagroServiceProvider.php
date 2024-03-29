<?php
namespace Diagro\Webhooks\Client;

use Diagro\Webhooks\Client\Commands\Register;
use Diagro\Webhooks\Client\Commands\Unregister;
use Diagro\Webhooks\Client\Http\Controllers\WebhookController;
use Diagro\Webhooks\Client\Middleware\BackendAppId;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
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
        Route::macro('webhooks', function (string $url, string $name = 'default') {
            return Route::post($url, WebhookController::class)->name("webhook-client-{$name}");
        });
        $this->webhooksRoutesFile();
        $this->loadRoutesFrom(__DIR__ . '/../routes/webhooks.php');

        //publish
        $this->publishes([
            __DIR__ . '/../configs/webhook-client.php' => config_path('webhook-client.php'),
            __DIR__ . '/../routes/webhooks-example.php' => base_path('routes/webhooks.php'),
        ]);

        //commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Register::class,
                Unregister::class,
            ]);
        }
    }


    private function webhooksRoutesFile()
    {
        $file = base_path('routes/webhooks.php');
        if(! file_exists($file)) {
            touch($file);
            file_put_contents($file, file_get_contents(__DIR__ . '/../routes/webhooks-example.php'));
        }
    }


}