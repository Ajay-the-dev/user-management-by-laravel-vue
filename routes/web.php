<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/debug-info', function() {
    phpinfo();
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

