<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello-api', function () {
    return ['message' => 'Hello from API!'];
});
