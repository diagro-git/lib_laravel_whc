<?php
namespace Diagro\Webhooks\Client\Commands;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Registreer deze webhook client bij een webhook server.
 * Standaard worden alle clients geregistreerd maar
 * je kan ook --name="..." gebruiken.
 *
 * In de config moet een extra veld bijkomen: registerUrl
 * Deze verwijst naar de volledige webhook register url
 * van de webhook server.
 */
class Register extends Command
{


    protected $signature = 'diagro:webhooks-register {--name=}';

    protected $description = 'Register a webhook client to the server';


    public function handle()
    {
        $name = $this->option('name');

        collect(config('webhook-client.configs'))
            ->each(function(array $config) use ($name) {
                if(empty($name) || $config['name'] === $name) {
                    $clientUrl = $this->getClientUrl($config['name']);
                    $response = $this->sendRegisterRequest($config['register_url'], $clientUrl, $config['signing_secret']);
                    if($response->ok()) {
                        $this->info(sprintf("Registratie succesvol voor %s.", $config['name']));
                    } else {
                        $this->error(sprintf("Registratie mislukt voor %s (url: %s, status: %d, error: %s)", $config['name'], $config['register_url'], $response->status(), $response->body()));
                    }
                }
            });
    }


    private function getClientUrl(string $name): string
    {
        return route('webhook-client-' . $name);
    }


    private function sendRegisterRequest(string $registerUrl, string $clientUrl, string $signing_secret): PromiseInterface|Response
    {
        return Http::withHeaders([
            'x-backend-token' => env('DIAGRO_BACKEND_TOKEN'),
            'accept' => 'application/json',
        ])->post($registerUrl, ['url' => $clientUrl, 'signed_secret' => $signing_secret]);
    }


}