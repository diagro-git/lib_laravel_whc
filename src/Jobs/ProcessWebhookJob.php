<?php
namespace Diagro\Webhooks\Client\Jobs;

use Illuminate\Support\Arr;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as BaseProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * This automatic injects the connection and/or queue name per config or global from the configs/webhook-client.php file.
 * If none is set, it uses the defaults set in the configs/queue.php
 */
abstract class ProcessWebhookJob extends BaseProcessWebhookJob
{

    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        //get config
        $config = $this->getConfig();

        //set connection
        $connection = Arr::get($config, 'connection', $this->getGlobalConnection());
        if(! empty($connection) && is_string($connection)) {
            $this->onConnection($connection);
        }

        //set queue name
        $queue = Arr::get($config, 'queue', $this->getGlobalQueue());
        if(! empty($queue) && is_string($queue)) {
            $this->onQueue($queue);
        }
    }

    protected function getGlobalConnection(): ?string
    {
        return config('webhook-client.connection');
    }

    protected function getGlobalQueue(): ?string
    {
        return config('webhook-client.queue');
    }

    protected function getConfig() : array
    {
        $configName = $this->webhookCall->name;
        return collect(config('webhook-client.configs'))
            ->first(fn(array $config) => $config['name'] == $configName);
    }

}