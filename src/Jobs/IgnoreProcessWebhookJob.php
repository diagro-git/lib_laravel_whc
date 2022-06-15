<?php
namespace Diagro\Webhooks\Client\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

/**
 * Set this for 'process_webhook_job' config property.
 * Because the config throws exceptions when value
 * is set to null or invalid class string.
 */
class IgnoreProcessWebhookJob extends ProcessWebhookJob
{

    public function handle()
    {}

}