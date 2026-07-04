<?php

use App\Http\Controllers\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('/chat', [ChatBotController::class, 'chat'])
    ->middleware('throttle:20,60');