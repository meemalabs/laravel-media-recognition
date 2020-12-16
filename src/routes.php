<?php

use Illuminate\Support\Facades\Route;
use Meema\MediaRecognition\Http\Controllers\IncomingWebhookController;

Route::post('/api/webhooks/media-recognition', IncomingWebhookController::class)->name('webhooks.media-recognition');
