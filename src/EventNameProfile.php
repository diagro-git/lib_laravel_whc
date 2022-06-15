<?php
namespace Diagro\Webhooks\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class EventNameProfile implements WebhookProfile
{

    public function shouldProcess(Request $request): bool
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'data' => 'required|array'
        ]);

        if(! $validator->fails()) {
            try {
                $data = $validator->validated();
                $allowedEvents = $this->getAllowedEvents();
                return in_array($data['name'], $allowedEvents);
            } catch(ValidationException $exception)
            {
                return false;
            }
        }

        return false;
    }

    private function getAllowedEvents(): array
    {
        $allowed = [];
        $routeName = Route::currentRouteName() ?? '';
        $configName = Str::after($routeName, 'webhook-client-');

        $config = collect(config('webhook-client.configs'))
                    ->first(fn(array $config) => $config['name'] == $configName);

        if(is_array($config) && isset($config['allowed_events']) && is_array($config['allowed_events']) && Arr::isList($config['allowed_events'])) {
            $allowed = $config['allowed_events'];
        }

        return $allowed;
    }

}