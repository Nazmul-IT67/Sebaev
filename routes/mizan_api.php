<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Web\CategoryController;
use App\Http\Controllers\Api\Web\MiniGoalController;
use App\Http\Controllers\Api\Web\OnBodingController;
use App\Http\Controllers\Api\Web\PostShareController;
use App\Http\Controllers\Api\Web\MovementShareController;
use App\Http\Controllers\Api\MovementResponseVideoController;


Route::group(['middleware' => ['jwt.verify']], function () {

    Route::controller(OnBodingController::class)->group(function () {
        Route::post('/react-on-boding', 'store');
        Route::post('/interest-on-boding', 'interest');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'getCategories');
    });

    Route::controller(MovementResponseVideoController::class)->group(function () {
        Route::post('/response-video/{id}', 'store');
        Route::get('/response-video/{id}', 'getVideo');
        Route::delete('/response-video-delete/{id}', 'delete');
    });

    Route::controller(MiniGoalController::class)->group(function () {
        Route::get('/today-mini-goal', 'getTodayMiniGoal');
    });

    Route::controller(PostShareController::class)->group(function () {
        Route::post('/post-share/{id}', 'postShare');
    });

    Route::controller(MovementShareController::class)->group(function () {
        Route::post('/movement-share/{id}', 'movementShare');
    });
});


