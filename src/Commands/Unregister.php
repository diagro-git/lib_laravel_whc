<?php
namespace Diagro\Webhooks\Client\Commands;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Unregistreer deze webhook client bij een webhook server.
 * Standaard worden alle clients geregistreerd maar
 * je kan ook --name="..." gebruiken.
 *
 * In de config moet een extra veld bijkomen: unregister_url
 * Deze verwijst naar de volledige webhook unregister url
 * van de webhook server.
 */
class Unregister extends Command
{


    protected $signature = 'diagro:webhooks-unregister {--name=}';

    protected $description = 'Unregister a webhook client to the server';


    public function handle()
    {
        $name = $this->option('name');

        collect(config('webhook-client.configs'))
            ->each(function(array $config) use ($name) {
                if(empty($name) || $config['name'] === $name) {
                    if(! isset($config['unregister_url'])) {
                        $clientUrl = $this->getClientUrl($config['name']);
                        $response = $this->sendUnregisterRequest($config['unregister_url'], $clientUrl);
                        if ($response->ok()) {
                            $this->info(sprintf("Unregistratie succesvol voor %s.", $config['name']));
                        } else {
                            $this->error(sprintf("Unregistratie mislukt voor %s (url: %s, status: %d, error: %s)", $config['name'], $config['unregister_url'], $response->status(), $response->body()));
                        }
                    } else {
                        $this->error(sprintf("Webhooks config heeft geen unregister_url veld (config: %s)", $config['name']));
                    }
                }
            });
    }


    private function getClientUrl(string $name): string
    {
        return route('webhook-client-' . $name);
    }


    private function sendUnregisterRequest(string $unregisterUrl, string $clientUrl): PromiseInterface|Response
    {
        return Http::withHeaders([
            'x-backend-token' => env('DIAGRO_BACKEND_TOKEN'),
            'accept' => 'application/json',
        ])->delete($unregisterUrl, ['url' => $clientUrl]);
    }


}