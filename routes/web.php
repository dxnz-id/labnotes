<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage', [
        'title' => "Muhammad La'azidannak Rusda",
        'description' => 'Chill'
    ]);
});

require __DIR__ . '/auth.php';
