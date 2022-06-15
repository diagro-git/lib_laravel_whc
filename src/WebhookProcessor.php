<?php
namespace Diagro\Webhooks\Client;

use Illuminate\Support\Arr;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookProcessor as BaseWebhookProcessor;

class WebhookProcessor extends BaseWebhookProcessor
{

    protected function processWebhook(WebhookCall $webhookCall): void
    {
        try {
            //get name of the event
            $eventName = $webhookCall->payload['name'];

            //get config
            $config = $this->getConfig($webhookCall->name);

            //get the job
            $job = Arr::get($config, "allowed_events.$eventName");
            if ($job === null) {
                throw new \Exception("No webhook client job found for event $eventName!");
            }

            //dispatch the job
            $job = new $job($webhookCall);
            $connection = Arr::get($config, 'connection', $this->getGlobalConnection());
            $queue = Arr::get($config, 'queue', $this->getGlobalQueue());

            if(! empty($connection)) {
                $job->onConnection($connection);
            }

            if(! empty($queue)) {
                $job->onQueue($queue);
            }

            $webhookCall->clearException();

            dispatch($job);
        } catch(\Exception $exception)
        {
            $webhookCall->saveException($exception);
            throw $exception;
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

    protected function getConfig($configName) : array
    {
        return collect(config('webhook-client.configs'))
            ->first(fn(array $config) => $config['name'] == $configName);
    }

}