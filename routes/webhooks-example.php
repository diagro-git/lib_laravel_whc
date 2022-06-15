<?php

use Illuminate\Support\Facades\Route;

/**
 * Route::webhooks('/app-name', 'webhook-app-name')
 *
 * The URL is already prefixed with '/webhooks'!
 * "app-name" is the name of the backend
 * application that sends the events
 */
Route::webhooks('/', 'default');