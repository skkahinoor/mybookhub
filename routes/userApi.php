<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProductController;

Route::get('/user/home', [HomeController::class, 'home']);
Route::get('/user/products', [ProductController::class, 'index']);
Route::get('/user/search-suggestions', [ProductController::class, 'suggestions']);
Route::get('/user/education-level', [HomeController::class, 'getSections']);
Route::get('/user/board/{id}', [HomeController::class, 'getcategories']);
Route::get('/user/class', [HomeController::class, 'getSubcategories']);
Route::get('/user/book-type', [HomeController::class, 'getBookTypes']);
Route::get('/user/language', [HomeController::class, 'getLanguages']);
