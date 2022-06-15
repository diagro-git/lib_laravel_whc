<?php
namespace Diagro\Webhooks\Client\Http\Controllers;

use Diagro\Webhooks\Client\WebhookProcessor;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookConfig;

class WebhookController
{

    public function __invoke(Request $request, WebhookConfig $config)
    {
        return (new WebhookProcessor($request, $config))->process();
    }

}