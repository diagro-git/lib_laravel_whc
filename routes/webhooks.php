<?php

use Illuminate\Support\Facades\Route;

Route::middleware('webhooks')
    ->prefix('/webhooks')
    ->group(base_path('routes/webhooks.php'));