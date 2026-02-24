<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\HomeController;

Route::get('/user/home', [HomeController::class, 'home']);
